<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoHtmlSitemap
 */


namespace Amasty\SeoHtmlSitemap\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Amasty\SeoHtmlSitemap\Model\ResourceModel\Page\Xlanding\CollectionFactory as LandingPageCollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Cms\Helper\Page as CmsPageHelper;
use Amasty\SeoHtmlSitemap\Helper\LandingPage as LandingPageHelper;
use Amasty\SeoHtmlSitemap\Helper\Data as SeoSitemapHelper;

class Sitemap extends AbstractModel
{
    /**
     * @var SeoSitemapHelper
     */
    private $helper;

    /**
     * @var CmsPageHelper
     */
    private $cmsPageHelper;

    /**
     * @var LandingPageHelper
     */
    private $landingPageHelper;

    /**
     * @var Stock
     */
    private $stockHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var PageCollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var LandingPageCollectionFactory
     */
    private $landingPageCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Tree
     */
    private $categoryTree;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        Stock $stockHelper,
        SeoSitemapHelper $helper,
        CmsPageHelper $cmsPageHelper,
        LandingPageHelper $landingPageHelper,
        CategoryRepository $categoryRepository,
        PageCollectionFactory $pageCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        LandingPageCollectionFactory $landingPageCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->helper = $helper;
        $this->cmsPageHelper = $cmsPageHelper;
        $this->landingPageHelper = $landingPageHelper;
        $this->stockHelper = $stockHelper;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->landingPageCollectionFactory = $landingPageCollectionFactory;
        $this->categoryTree = $categoryTree;
        $this->productMetadata = $productMetadata;
    }

    public function getCMSPages()
    {
        $cmsPagesList = [];
        $pageCollection = $this->_getPageCollection();

        foreach ($pageCollection as $pageItem) {
            $cmsPagesList[] = [
                'title' => $pageItem->getTitle(),
                'url'   => $this->cmsPageHelper->getPageUrl($pageItem->getId())
            ];
        }

        return $cmsPagesList;
    }

    public function getLinks()
    {
        $links     = [];
        $addLinks  = $this->helper->getAdditionalLinks();
        $linksList = preg_split('/$\R?^/m', $addLinks);
        foreach ($linksList as $link) {
            if (strpos($link, ',') === false) {
                continue;
            }

            list($linkText, $linkUrl) = explode(',', trim($link), 2);
            if (empty($linkText) || empty($linkUrl)) {
                continue;
            }

            $links[] = [
                'title' => htmlspecialchars(trim($linkText)),
                'url'   => htmlspecialchars(trim($linkUrl))
            ];
        }

        return $links;
    }

    public function getLandingPages()
    {
        if (!$this->helper->isModuleOutputEnabled('Amasty_Xlanding')) {
            return [];
        }

        $landingPagesCollection = $this->_getLandingPageCollection();

        $landingPagesList = [];
        foreach ($landingPagesCollection as $pageItem) {
            if ($pageItem->getTitle()) {
                $landingPagesList[] = [
                    'title' => $pageItem->getTitle(),
                    'url'   => $this->landingPageHelper->getPageUrl($pageItem->getId())
                ];
            }
        }

        return $landingPagesList;
    }

    public function getProducts()
    {
        $productCollection = $this->_getProductCollection();
        if ($this->helper->getProductsLimit()) {
            $productCollection->setPageSize($this->helper->getProductsLimit());
        }

        if ($this->helper->getProductsSplitByLetter()) {
            $letterGroups = [];
            foreach ($productCollection as $product) {
                $letter = strtoupper(substr($product->getName(), 0, 1));
                if (is_numeric($letter) || $letter == ' ') {
                    $letter = '#';
                }

                $letterGroups[$letter]['letter'] = $letter;
                $letterGroups[$letter]['items'][] = $product;
            }

            return $letterGroups;
        }

        return $productCollection;
    }

    public function getCategories()
    {
        $parentId = $this->storeManager->getStore()->getRootCategoryId();

        if ($this->helper->getCategoriesShowAs() == SeoSitemapHelper::CATEGORY_LIST_TYPE) {
            $catIds = $this->_getChildCategoryIds($parentId);
            $catIds = array_diff($catIds, $this->getExcludeCategoryIds());

            $categoryList = $this->categoryCollectionFactory->create()
                ->addIdFilter($catIds)
                ->addAttributeToSelect('*');

            return $categoryList;
        }

        $categoryCollection = $this->_getCategoryCollection();
        $tree = $this->categoryTree->load();
        $root = $tree->getNodeById($parentId);

        if ($root && $root->getId() == 1) {
            $root->setName(__('Root'));
        }

        $tree->addCollectionData($categoryCollection, true);

        return $this->_nodeToArray($root);
    }

    /**
     * @return array
     */
    private function getExcludeCategoryIds()
    {
        $ids = $this->helper->getExcludeCategoryIds();
        $ids = explode(',', $ids);

        return $ids;
    }

    protected function _getExcludeCMSPages()
    {
        $excludeCMSPages       = [];
        $excludeCMSPagesConfig = $this->helper->getExcludeCMSPages();
        if ($excludeCMSPagesConfig !== null) {
            $excludeCMSPagesConfigList = explode(",", $excludeCMSPagesConfig);
            foreach ($excludeCMSPagesConfigList as $item) {
                $excludeCMSPages[] = trim($item);
            }
        }

        return $excludeCMSPages;
    }

    protected function _getPageCollection()
    {
        $excludeCMSPages = $this->_getExcludeCMSPages();
        $collection = $this->pageCollectionFactory->create();

        if (!empty($excludeCMSPages)) {
            $collection->addFilter('identifier', ['nin' => $excludeCMSPages], 'public');
        }
        $collection->setOrder('title', 'ASC');
        $collection->addFilter('is_active', '1');
        $collection->addStoreFilter($this->storeManager->getStore()->getId());

        return $collection;
    }

    protected function _getLandingPageCollection()
    {
        $collection = $this->landingPageCollectionFactory->create();

        $collection->addFilter('is_active', '1');
        $collection->addStoreFilter($this->storeManager->getStore()->getId());

        return $collection;
    }

    protected function _getProductCollection()
    {
        $collection = $this->productCollectionFactory->create();

        $collection->addAttributeToSelect(['name', 'url_key', 'thumbnail', 'thumbnail_label', 'url_path', 'image']);
        $collection->addStoreFilter($this->storeManager->getStore()->getId());
        $collection->addUrlRewrite();

        $collection->addAttributeToFilter('status', 1);
        $collection->addAttributeToFilter('visibility', [
            'in' => [
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
            ]
        ]);

        $collection->addAttributeToSort('name', 'ASC');

        if ($this->helper->getProductsHideOutOfStock()) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }

        return $collection;
    }

    protected function _getCategoryCollection()
    {
        $rootId = $this->storeManager->getStore()->getRootCategoryId();
        $entityField = $this->productMetadata->getEdition() != 'Community' ? 'row_id' : 'entity_id';

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect(['url_key', 'name', 'thumbnail', 'image'])
            ->addFieldToFilter('path', ['like'=> "1/$rootId/%"])
            ->addAttributeToFilter('level', ['gt' => 1])
            ->addAttributeToFilter('is_active', 1)
            ->addFieldToFilter($entityField, ['nin' => $this->getExcludeCategoryIds()])
            ->addUrlRewriteToResult();

        return $collection;
    }

    protected function _getChildCategoryIds($parentId)
    {
        $category = $this->categoryRepository->get($parentId);
        $childIds = $category->getAllChildren();
        $catIds = explode(',', $childIds);

        if (($key = array_search($parentId, $catIds)) !== false) {
            unset($catIds[$key]);
        }

        return $catIds;
    }

    protected function _nodeToArray(\Magento\Framework\Data\Tree\Node $node)
    {
        $result = [];
        $result['category_id'] = $node->getId();
        $result['name']        = $node->getName();
        $result['level']       = $node->getLevel();
        $result['url']         = $node->getRequestPath();
        $result['children']    = [];

        foreach ($node->getChildren() as $child) {
            $result['children'][] = $this->_nodeToArray($child);
        }

        return $result;
    }
}
