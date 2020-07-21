<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Psr\Log\LoggerInterface;

class ProductInitAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->coreRegistry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->scopeConfig->getValue(
            'amseorichdata/breadcrumbs/extend',
            ScopeInterface::SCOPE_STORE
        )) {
            return;
        }

        $category = $this->coreRegistry->registry('current_category');
        if ($category) {
            return;
        }

        $product = $observer->getProduct();
        $categories = $this->getCategoriesForStore($product);

        $select = $categories->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['entity_id'])
            ->order('level DESC')
            ->limit(1);

        $categoryId = $categories->getConnection()->fetchOne($select);
        if ($categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $this->coreRegistry->register('current_category', $category);
            } catch (NoSuchEntityException $e) {
                $this->logger->debug($e->getMessage());
            }
        }
    }

    /**
     * @param Product $product
     * @return CategoryCollection
     */
    private function getCategoriesForStore($product)
    {
        $rootCategory = $this->categoryRepository->get(
            $this->storeManager->getStore()->getRootCategoryId()
        );

        return $product->getCategoryCollection()
            ->addPathsFilter(trim($rootCategory->getPath(), '/') . '/');
    }
}
