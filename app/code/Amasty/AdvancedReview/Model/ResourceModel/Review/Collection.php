<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Review;

use Amasty\AdvancedReview\Model\ResourceModel\Images as ImagesModel;
use Amasty\AdvancedReview\Model\ResourceModel\Vote as VoteModel;
use Amasty\AdvancedReview\Model\Sources\Filter;
use Magento\Framework\DB\Select;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Collection
 * @package Amasty\AdvancedReview\Model\ResourceModel\Review
 */
class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * Reviews loaded for this products
     *
     * @var array
     */
    private $productIds = [];

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Review\Helper\Data $reviewData,
        \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductMetadataInterface $productMetadata,
        AttributeRepositoryInterface $attributeRepository,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $reviewData,
            $voteFactory,
            $storeManager,
            $connection,
            $resource
        );
        $this->productMetadata = $productMetadata;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        // add out fields to select
        $this->getSelect()->columns(['detail.like_about', 'detail.not_like_about', 'detail.guest_email']);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFlag('filter_by_stars') || $this->getFlag('join_rating_summary')) {
            $this->getSelect()->join(
                ['votes' => $this->getTable('rating_option_vote')],
                'votes.review_id = main_table.review_id',
                ['rating_summary' => 'FLOOR(sum(`votes`.`value`)/count(`votes`.`value`))']
            );
            $this->getSelect()->group('main_table.review_id');
        }

        if ($this->getFlag('join_helpful')) {
            $this->getSelect()->joinLeft(
                ['customer_vote' => $this->getTable(VoteModel::TABLE_NAME)],
                'customer_vote.review_id = main_table.review_id AND customer_vote.type=1',
                ['helpful' => 'count(DISTINCT(`customer_vote`.`vote_id`))']
            );
            $this->getSelect()->group('main_table.review_id');
        }

        if ($this->getFlag(Filter::WITH_IMAGES)) {
            $this->getSelect()->join(
                ['images' => $this->getTable(ImagesModel::TABLE_NAME)],
                'images.review_id = main_table.review_id',
                []
            );
            $this->getSelect()->group('main_table.review_id');
        }

        parent::_renderFiltersBefore();
    }

    /**
     * remove filtering used in group
     *
     * @return Select
     */
    public function getSelectCountSql()
    {
        $countSql = parent::getSelectCountSql();
        $countSql->reset(Select::HAVING);

        return $countSql;
    }

    /**
     * fix getSize when reviews filtering by rating
     *
     * @return bool|int|null
     */
    public function getSize()
    {
        if ($itemsCount = $this->getFlag('items_count')) {
            return $itemsCount;
        }

        return parent::getSize();
    }

    /**
     * @param int|string $entity
     * @param int|array $pkValue
     *
     * @return $this
     */
    public function addEntityFilter($entity, $pkValue)
    {
        $reviewEntityTable = $this->getReviewEntityTable();
        if (is_numeric($entity)) {
            $this->addFilter('entity', $this->getConnection()->quoteInto('main_table.entity_id=?', $entity), 'string');
        } elseif (is_string($entity)) {
            $this->_select->join(
                $reviewEntityTable,
                'main_table.entity_id=' . $reviewEntityTable . '.entity_id',
                ['entity_code']
            );

            $this->addFilter(
                'entity',
                $this->getConnection()->quoteInto($reviewEntityTable . '.entity_code=?', $entity),
                'string'
            );
        }

        if (!is_array($pkValue)) {
            $condition = $this->getConnection()->quoteInto('main_table.entity_pk_value=?', $pkValue);
        } else {
            $condition = $this->getConnection()->quoteInto('main_table.entity_pk_value in (?)', $pkValue);
        }
        $this->addFilter(
            'entity_pk_value',
            $condition,
            'string'
        );

        return $this;
    }

    /**
     * Filter Product by Categories
     *
     * @param array $categoriesFilter
     * @return $this
     */
    public function addCategoriesFilter(array $categoriesFilter)
    {
        foreach ($categoriesFilter as $conditionType => $values) {
            $categorySelect = $this->getConnection()->select()->from(
                ['cat' => $this->getTable('catalog_category_product')],
                'cat.product_id'
            )->where($this->getConnection()->prepareSqlCondition('cat.category_id', ['in' => $values]));
            $selectCondition = [
                $this->mapConditionType($conditionType) => $categorySelect
            ];
            $this->getSelect()->where($this->getConnection()
                ->prepareSqlCondition('main_table.entity_pk_value', $selectCondition));
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     *
     * @return $this
     */
    public function addItem(\Magento\Framework\DataObject $item)
    {
        $this->productIds[] = $item->getEntityPkValue();

        return parent::addItem($item);
    }

    /**
     * @return array
     */
    public function getProductIds()
    {
        return $this->productIds;
    }

    /**
     * @return array
     */
    public function getOrder()
    {
        return $this->_orders;
    }

    /**
     * @param array $orders
     *
     * @return $this
     */
    public function setOrders($orders)
    {
        $this->_orders = $orders;
        return $this;
    }

    /**
     * Map equal and not equal conditions to in and not in
     *
     * @param string $conditionType
     * @return mixed
     */
    private function mapConditionType($conditionType)
    {
        $conditionsMap = [
            'eq' => 'in',
            'neq' => 'nin'
        ];
        return $conditionsMap[$conditionType] ?? $conditionType;
    }
}
