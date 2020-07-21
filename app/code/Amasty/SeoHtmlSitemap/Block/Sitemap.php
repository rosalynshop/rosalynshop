<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoHtmlSitemap
 */


namespace Amasty\SeoHtmlSitemap\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Amasty\SeoHtmlSitemap\Helper\Data as SitemapHelper;
use Amasty\SeoHtmlSitemap\Helper\Renderer as RendererHelper;
use Amasty\SeoHtmlSitemap\Model\SitemapFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

class Sitemap extends Template
{
    /**
     * @var array
     */
    protected $sitemapData;

    /**
     * @var SitemapHelper
     */
    private $helper;

    /**
     * @var RendererHelper
     */
    private $helperRenderer;

    /**
     * @var SitemapFactory
     */
    private $sitemapFactory;

    public function __construct(
        Context $context,
        SitemapHelper $helper,
        RendererHelper $helperRenderer,
        SitemapFactory $sitemapFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->helperRenderer = $helperRenderer;
        $this->sitemapFactory = $sitemapFactory;
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        /** @var \Amasty\SeoHtmlSitemap\Model\Sitemap $sitemapDataModel */
        $sitemapDataModel = $this->sitemapFactory->create();
        $this->sitemapData = [
            'links'         => $sitemapDataModel->getLinks(),
            'linksTitle'    => $this->helper->getLinksTitle(),
            'linksColumns'  => $this->helper->getLinksNumberOfColumns(),
            'title'         => $this->helper->getPageTitle(),
            'search'        => $this->helper->canShowSearchField()
        ];

        //category collection
        if ($this->helper->canShowCategories()) {
            $this->sitemapData['categories']         = $sitemapDataModel->getCategories();
            $this->sitemapData['categoriesColumns']  = $this->helper->getCategoriesNumberOfColumns();
            $this->sitemapData['categoriesTitle']    = $this->helper->getCategoriesTitle();
            $this->sitemapData['categoriesGrid']     = $this->helper->getCategoriesShowAs();
        }

        //product collection
        if ($this->helper->canSnowProducts()) {
            $this->sitemapData['products']               = $sitemapDataModel->getProducts();
            $this->sitemapData['productsLetterSplit']    = $this->helper->getProductsSplitByLetter();
            $this->sitemapData['productsTitle']          = $this->helper->getProductsTitle();
            $this->sitemapData['productsColumns']        = $this->helper->getProductsNumberOfColumns();
        }

        //pages
        if ($this->helper->canShowCmsPages()) {
            $this->sitemapData['pages']              = $sitemapDataModel->getCMSPages();
            $this->sitemapData['pagesTitle']         = $this->helper->getCMSHeaderTitle();
            $this->sitemapData['pagesColumns']       = $this->helper->getCMSNumberOfColumns();
        }

        //landing pages
        if ($this->helper->canShowLandingPages()) {
            $this->sitemapData['landingPages']       = $sitemapDataModel->getLandingPages();
            $this->sitemapData['landingTitle']       = $this->helper->getLandingTitle();
            $this->sitemapData['landingColumns']     = $this->helper->getLandingNumberOfColumns();
        }

        $this->addData($this->sitemapData);
        return parent::_beforeToHtml();
    }

    /**
     * @return bool
     */
    public function canShowSearchField()
    {
        return $this->helper->canShowSearchField();
    }

    /**
     * @return bool
     */
    public function canShowCMSPages()
    {
        if (!$this->helper->canShowCMSPages() || $this->isEmptyData($this->getSitemapData('pages'))) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canShowLinks()
    {
        if ($this->isEmptyData($this->getSitemapData('links'))) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canShowLandingPages()
    {
        if (!$this->helper->isModuleOutputEnabled('Amasty_Xlanding') || !$this->helper->canShowLandingPages()
            || $this->isEmptyData($this->getSitemapData('landingPages'))) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canShowProducts()
    {
        if (!$this->helper->canSnowProducts() || $this->isEmptyData($this->getSitemapData('products'))) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canShowCategories()
    {
        if (!$this->helper->canShowCategories() || $this->isEmptyData($this->getSitemapData('categories'))) {
            return false;
        }

        return true;
    }

    /**
     * @param string $index
     * @return bool
     */
    public function getSitemapData($index = '')
    {
        if (!isset($this->sitemapData[$index])) {
            return false;
        }

        return $this->sitemapData[$index];
    }

    /**
     * @return string
     */
    public function getProductShowType()
    {
        return ($this->helper->getProductsSplitByLetter()) ? 'product_split' : 'product';
    }

    /**
     * @return string
     */
    public function getCategoryShowType()
    {
        $type = 'categories_list';

        if ($this->helper->getCategoriesShowAs() == SitemapHelper::CATEGORY_TREE_TYPE) {
            $type = 'categories_tree';
        }

        return $type;
    }

    /**
     * @return bool
     */
    public function getCategories()
    {
        $categories = $this->getSitemapData('categories');

        return ($this->isTree()) ? $categories['children'] : $categories;
    }

    /**
     * @return bool
     */
    public function isTree()
    {
        if ($this->helper->getCategoriesShowAs() == SitemapHelper::CATEGORY_TREE_TYPE) {
            return true;
        }

        return false;
    }

    /**
     * @param $collection
     * @param $type
     * @param int $columnSize
     * @param bool $isTree
     * @return string
     */
    public function renderChunks($collection, $type, $columnSize = 1, $isTree = false)
    {
        return $this->helperRenderer->renderArrayChunks($collection, $type, $columnSize, $isTree);
    }

    /**
     * @return mixed
     */
    public function getSortOrder()
    {
        return $this->helper->getSortOrder();
    }

    /**
     * @param array|AbstractCollection $data
     * @return bool
     */
    private function isEmptyData($data)
    {
        $result = false;
        if ((is_array($data) && empty($data))
            || ($data instanceof AbstractCollection && $data->getSize() == 0)
        ) {
            $result = true;
        }

        return $result;
    }
}
