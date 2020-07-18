<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Comment\Grid;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Model\ResourceModel\Comment as CommentResource;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

class Collection extends \Amasty\AdvancedReview\Model\ResourceModel\Comment\Collection implements SearchResultInterface
{
    /**
     * @var array
     */
    protected $_map = [
        'fields' => [
            'title' => 'review.title',
            'nickname' => 'main_table.nickname',
            'store_id' => 'main_table.store_id',
        ]
    ];

    /**
     * @var AggregationInterface
     */
    private $aggregations;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $eventPrefix = 'amasty_advancedreview_comment',
        $eventObject = 'review_comment_grid_collection',
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, CommentResource::class);
        $this->setMainTable(CommentInterface::TABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    protected function _renderFiltersBefore()
    {
        $this->getSelect()->join(
            ['review' => $this->getTable('review_detail')],
            'main_table.review_id = review.review_id',
            ['title']
        );
        parent::_renderFiltersBefore();
    }
}
