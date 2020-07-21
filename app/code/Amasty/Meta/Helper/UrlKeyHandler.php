<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Helper;

use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

class UrlKeyHandler extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;
    protected $_tablePrefix;
    protected $_productTypeId;
    protected $_urlPathId;
    protected $_urlKeyId;
    protected $_pageSize = 100;

    /**
     * Base product target path.
     */
    const BASE_PRODUCT_TARGET_PATH  = 'catalog/product/view/id/%d';
    /**
     * Base path for product in category
     */
    const BASE_PRODUCT_CATEGORY_TARGET_PATH = 'catalog/product/view/id/%d/category/%d';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * Product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var UrlRewriteCollectionFactory
     */
    private $rewriteCollectionFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Meta\Helper\Data $helperData,
        UrlRewriteCollectionFactory $rewriteCollectionFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->resource = $resourceConnection;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_helperData = $helperData;
        parent::__construct($context);
        $this->_construct();
        $this->rewriteCollectionFactory = $rewriteCollectionFactory;
        $this->productMetadata = $productMetadata;
    }

    public function _construct()
    {
        $this->_connection = $this->resource->getConnection('core_write');

        //product type id
        $select = $this->_connection->select()->from($this->resource->getTableName('eav_entity_type'))
            ->where("entity_type_code = 'catalog_product'");
        $this->_productTypeId = $this->_connection->fetchOne($select);

        //url path id
        $select = $this->_connection->select()->from($this->resource->getTableName('eav_attribute'))
            ->where("entity_type_id = $this->_productTypeId AND (attribute_code = 'url_path')");
        $this->_urlPathId = $this->_connection->fetchOne($select);

        //url key id
        $select = $this->_connection->select()->from($this->resource->getTableName('eav_attribute'))
            ->where("entity_type_id = $this->_productTypeId AND (attribute_code = 'url_key')");
        $this->_urlKeyId = $this->_connection->fetchOne($select);
    }

    /**
     * @param $urlKeyTemplate
     * @param array $storeIds
     * @param int $page
     */
    public function process($urlKeyTemplate, $storeIds = [], $page = 1)
    {
        $storeEntities = $this->_getStores($storeIds);

        foreach ($storeEntities as $store) {

            $products = $this->_productFactory->create()->getCollection()
                ->addAttributeToSelect('*')
                ->setCurPage($page)
                ->setPageSize($this->getPageSize())
                ->setStore($store);

            foreach ($products as $product) {
                $this->processProduct($product, $store, $urlKeyTemplate);
            }
        }

        //default values
        $products = $this->_productFactory->create()->getCollection()
            ->addAttributeToSelect('*')
            ->setCurPage($page)
            ->setPageSize($this->getPageSize());

        foreach ($products as $product) {
            $this->processProduct($product, null, $urlKeyTemplate);
        }
    }

    public function estimate($storeIds = [])
    {
        $products = $this->_productFactory->create()->getCollection();

        if ($storeIds) {
            $products->setStore($storeIds[0]);
        }

        return $products->getSize();
    }

    protected function _getStores($storeIds)
    {
        $storeEntities =$this->_storeManager->getStores(true, true);
        if (! empty($storeIds)) {
            foreach ($storeEntities as $key => $storeEntity) {
                if (! in_array($key, $storeIds)) {
                    unset($storeEntities[$key]);
                }
            }
        }

        return $storeEntities;
    }

    /**
     * @param        $product
     * @param        $store
     * @param string $urlKeyTemplate
     */
    public function processProduct($product, $store, $urlKeyTemplate = '')
    {
        if (empty($urlKeyTemplate)) {
            $urlKeyTemplate = trim($this->scopeConfig->getValue(
                'ammeta/product/url_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getCode()
            ));
        }

        if (empty($urlKeyTemplate)) {
            return;
        }

        $storeId = ($store && $store->getId()) ? $store->getId() : 0;
        $product->setStoreId($storeId);
        $urlKey = $this->_helperData->cleanEntityToCollection()
            ->addEntityToCollection($product)
            ->parse($urlKeyTemplate, true);

        $urlKey = $product->formatUrlKey($urlKey);

        //update url_key
        $this->_updateUrlKey($product, $storeId, $urlKey);

        $urlSuffix = $this->scopeConfig->getValue(
            'catalog/seo/product_url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        //update url_path
        $this->_updateUrlPath($product, $storeId, $urlKey, $urlSuffix);
        $this->_updateUrlRewrite($product, $storeId, $urlKey, $urlSuffix);

        $product->setUrlKey($urlKey);
    }

    /**
     * @param $product
     * @param $storeId
     * @param $urlKey
     * @param string $urlSuffix
     */
    protected function _updateUrlKey($product, $storeId, $urlKey, $urlSuffix = '')
    {
        $this->_updateAttribute($this->_urlKeyId, $product, $storeId, $urlKey, $urlSuffix);
    }

    /**
     * @param $product
     * @param $storeId
     * @param $urlKey
     * @param string $urlSuffix
     */
    protected function _updateUrlPath($product, $storeId, $urlKey, $urlSuffix = '')
    {
        $this->_updateAttribute($this->_urlPathId, $product, $storeId, $urlKey, $urlSuffix);
    }

    /**
     * @param $product
     * @param $storeId
     * @param $urlKey
     * @param string $urlSuffix
     */
    protected function _updateUrlRewrite($product, $storeId, $urlKey, $urlSuffix = '')
    {
        /** @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $collection */
        $collection = $this->rewriteCollectionFactory->create();
        $collection->addStoreFilter([$storeId]);
        $productPath = 'catalog/product/view/id/';
        $collection->getSelect()->where(
            '(target_path = ?',
            $productPath . $product->getId()
        )
        ->orWhere('target_path like ?)', $productPath . $product->getId() . '/%');
       

        if ($collection->getSize()) {
            foreach ($collection as $urlRewrite) {
                $requestPath = $urlRewrite->getRequestPath();
                $requestPathArray = explode('/', $requestPath);
                $oldPath = end($requestPathArray);
                $newPath = $urlKey;
                if ($urlSuffix && strpos($oldPath, $urlSuffix) !== false) {
                    $newPath .= $urlSuffix;
                }

                $newPath = str_replace($oldPath, $newPath, $requestPath);
                $urlRewrite->setRequestPath($newPath);

                try {
                    $urlRewrite->save();
                } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/amasty_meta.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $message = __(
                        'Request path "%1" for product with ID %2 is already exists',
                        $urlRewrite->getRequestPath(), $urlRewrite->getEntityId()
                    );
                    $logger->warn($message);
                    continue;
                }
            }
        }
    }

    /**
     * @param $attributeId
     * @param $product
     * @param $storeId
     * @param $urlKey
     * @param $urlSuffix
     */
    protected function _updateAttribute($attributeId, $product, $storeId, $urlKey, $urlSuffix)
    {
        $table  = 'catalog_product_entity_varchar';
        $entityField = $this->productMetadata->getEdition() != 'Community' ? 'row_id' : 'entity_id';
        $entityValue = $product->getData($entityField);

        $select = $this->_connection->select()->from($this->resource->getTableName($table))
            ->where("attribute_id = $attributeId AND $entityField = $entityValue AND store_id = {$storeId}");
        $row    = $this->_connection->fetchRow($select);

        if ($row) {
            $this->_connection->update(
                $this->resource->getTableName($table),
                ['value' => $urlKey . $urlSuffix],
                "attribute_id = $attributeId AND $entityField = $entityValue AND store_id = {$storeId}"
            );
        } else {
            $data = [
                'attribute_id' => $attributeId,
                $entityField => $entityValue,
                'store_id' => $storeId,
                'value' => $urlKey . $urlSuffix
            ];
            $this->_connection->insert($this->resource->getTableName($table), $data);
        }
    }

    public function getPageSize()
    {
        return $this->_pageSize;
    }

    /**
     * @param int $productId
     * @param int|null $categoryId
     * @return string
     */
    protected function _getProductTargetPath($productId, $categoryId = null)
    {
        return empty($categoryId) ?
            sprintf(self::BASE_PRODUCT_TARGET_PATH, $productId) :
            sprintf(self::BASE_PRODUCT_CATEGORY_TARGET_PATH, $productId, $categoryId);
    }
}
