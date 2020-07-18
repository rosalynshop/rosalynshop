<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Indexer\Catalog\Category\Product;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Search\Request\DimensionFactory;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TableResolver
 * @package Amasty\AdvancedReview\Model\Indexer\Catalog\Category\Product
 */
class TableResolver
{
    const MAIN_INDEX_TABLE = 'catalog_category_product_index';

    /**
     * @var IndexScopeResolver
     */
    private $tableResolver;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DimensionFactory
     */
    private $dimensionFactory;

    public function __construct(
        IndexScopeResolver $tableResolver,
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        DimensionFactory $dimensionFactory
    ) {
        $this->tableResolver = $tableResolver;
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->dimensionFactory = $dimensionFactory;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getTableName($storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID)
    {
        /** @var Dimension $catalogCategoryProductDimension */
        $catalogCategoryProductDimension = $this->dimensionFactory->create([
            'name' => \Magento\Store\Model\Store::ENTITY,
            'value' => $storeId
        ]);

        $tableName = $this->tableResolver->resolve(
            self::MAIN_INDEX_TABLE,
            [
                $catalogCategoryProductDimension
            ]
        );
        if (!$this->resource->getConnection()->isTableExists($tableName)) {
            $tableName = self::MAIN_INDEX_TABLE;
        }

        return $this->resource->getTableName($tableName);
    }

    /**
     * @param int $categoryId
     *
     * @return array
     */
    public function getProductIds($categoryId)
    {
        $connection = $this->resource->getConnection();
        $sql = $connection->select()->from(
            $this->getTableName($this->storeManager->getStore()->getId()),
            'product_id'
        )->where('category_id = ?', $categoryId);

        return array_keys($connection->fetchAssoc($sql));
    }
}
