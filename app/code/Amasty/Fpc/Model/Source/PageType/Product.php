<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Magento\CatalogUrlRewrite\Model\ResourceModel\Category\Product as CatalogProductUrlRewrite;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite as UrlRewrite;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

class Product extends Rewrite
{
    /**
     * @var string
     */
    protected $rewriteType = UrlRewrite::ENTITY_TYPE_PRODUCT;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        UrlRewriteCollectionFactory $rewriteCollectionFactory,
        UrlInterface $urlBuilder,
        Emulation $appEmulation,
        State $appState,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        $isMultiStoreMode = false,
        array $stores = [],
        \Closure $filterCollection = null
    ) {
        parent::__construct(
            $rewriteCollectionFactory,
            $urlBuilder,
            $appEmulation,
            $appState,
            $storeManager,
            $isMultiStoreMode,
            $stores,
            $filterCollection
        );
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $storeId
     * @return UrlRewriteCollection
     */
    protected function getEntityCollection($storeId)
    {
        $collection = parent::getEntityCollection($storeId);

        if (!$this->isUseCategoriesPathForProductUrls($storeId)) {
            $collection->getSelect()->joinLeft(
                ['relation' => $collection->getTable(CatalogProductUrlRewrite::TABLE_NAME)],
                'main_table.url_rewrite_id = relation.url_rewrite_id',
                ['relation.category_id', 'relation.product_id']
            );
            $collection->getSelect()->where('relation.category_id IS NULL');
        }

        return $collection;
    }

    /**
     * @param $storeId
     * @return bool
     */
    private function isUseCategoriesPathForProductUrls($storeId)
    {
        if ((int)$storeId) {
            return $this->scopeConfig->isSetFlag(
                \Magento\Catalog\Helper\Product::XML_PATH_PRODUCT_URL_USE_CATEGORY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $this->scopeConfig->isSetFlag(\Magento\Catalog\Helper\Product::XML_PATH_PRODUCT_URL_USE_CATEGORY);
    }
}
