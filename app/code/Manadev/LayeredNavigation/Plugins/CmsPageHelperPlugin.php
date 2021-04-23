<?php

namespace Manadev\LayeredNavigation\Plugins;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Store\Model\StoreManagerInterface;
use Manadev\LayeredNavigation\Engine;

class CmsPageHelperPlugin
{
    /**
     * @var Engine
     */
    protected $engine;
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    public function __construct(Engine $engine,
        PageFactory $pageFactory, StoreManagerInterface $storeManager,
        Registry $coreRegistry, CategoryRepositoryInterface $categoryRepository)
    {
        $this->engine = $engine;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->categoryRepository = $categoryRepository;
    }

    public function aroundPrepareResultPage($subject, callable $proceed,
        Action $action, $pageId = null)
    {
        /** @var \Magento\Cms\Model\Page $page */
        $page = $this->pageFactory->create();
        if ($pageId !== null) {
            $page->setStoreId($this->storeManager->getStore()->getId());
            $page->load($pageId);
        }

        if ($categoryId = $page->getData('mana_layered_navigation_category_id')) {
            $category = $this->categoryRepository->get($categoryId,
                $this->storeManager->getStore()->getId());
            $this->coreRegistry->register('current_category', $category);
        }

        /* @var Page|bool $pageResult */
        if (!($pageResult = $proceed($action, $pageId))) {
            return $pageResult;
        }

        $applied = $this->isFilterApplied();

        /* @var ListProduct $productList */

        if ($page->getData('mana_hide_products_if_no_filters_applied') && !$applied) {
            $productList = $pageResult->getLayout()->getBlock('category.products.list');
            $productList->setData('manadev_hide', true);
        }

        if ($page->getData('mana_hide_content_if_filter_applied') && $applied) {
            $this->coreRegistry->register('mana_hide_content', true);
        }

        return $pageResult;
    }

    protected function isFilterApplied() {
        foreach ($this->engine->getFilters() as $engineFilter) {
            $appliedOptions = $engineFilter->getAppliedOptions();
            if ($appliedOptions !== false) {
                return true;
            }
        }

        return false;
    }

}
