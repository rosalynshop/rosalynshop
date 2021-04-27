<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Repository;

use Amasty\Fpc\Api\Data\FlushesLogInterface;
use Amasty\Fpc\Api\FlushesLogRepositoryInterface;
use Amasty\Fpc\Model\FlushesLogFactory;
use Amasty\Fpc\Model\ResourceModel\FlushesLog as FlushesLogResource;
use Amasty\Fpc\Model\ResourceModel\FlushesLog\CollectionFactory;
use Amasty\Fpc\Model\ResourceModel\FlushesLog\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

class FlushesLogRepository implements FlushesLogRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var FlushesLogFactory
     */
    private $flushesLogFactory;

    /**
     * @var FlushesLogResource
     */
    private $flushesLogResource;

    /**
     * @var array
     */
    private $flushesLog;

    /**
     * @var CollectionFactory
     */
    private $flushesLogCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        FlushesLogFactory $flushesLogFactory,
        FlushesLogResource $flushesLogResource,
        CollectionFactory $flushesLogCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->flushesLogFactory = $flushesLogFactory;
        $this->flushesLogResource = $flushesLogResource;
        $this->flushesLogCollectionFactory = $flushesLogCollectionFactory;
    }

    public function save(FlushesLogInterface $flushesLog)
    {
        try {
            $this->flushesLogResource->save($flushesLog);
            unset($this->activitys[$flushesLog->getLogId()]);
        } catch (\Exception $e) {
            if ($flushesLog->getLogId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save log with ID %1. Error: %2',
                        [$flushesLog->getLogId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new activity. Error: %1', $e->getMessage()));
        }

        return $flushesLog;
    }

    public function getById($id)
    {
        if (!isset($this->flushesLog[$id])) {
            /** @var \Amasty\Fpc\Model\FlushesLog $flushesLog */
            $activity = $this->flushesLogResource->create();
            $this->flushesLogResource->load($activity, $id);
            if (!$flushesLog->getLogId()) {
                throw new NoSuchEntityException(__('Activity with specified ID "%1" not found.', $id));
            }
            $this->flushesLog[$id] = $flushesLog;
        }

        return $this->flushesLog[$id];
    }

    public function delete(FlushesLogInterface $flushesLog)
    {
        try {
            $this->flushesLogResource->delete($flushesLog);
            unset($this->flushesLog[$flushesLog->getLogId()]);
        } catch (\Exception $e) {
            if ($flushesLog->getLogId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove log with ID %1. Error: %2',
                        [$flushesLog->getLogId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove log. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteById($id)
    {
        $flushesLogModel = $this->getById($id);
        $this->delete($flushesLogModel);

        return true;
    }

    /**
     * @return FlushesLogInterface
     */
    public function getEmptyFlushesLogModel()
    {
        return $this->flushesLogFactory->create();
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\Fpc\Model\ResourceModel\Activity\Collection $activityCollection */
        $activityCollection = $this->activityCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $activityCollection);
        }

        $searchResults->setTotalCount($activityCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $activityCollection);
        }

        $activityCollection->setCurPage($searchCriteria->getCurrentPage());
        $activityCollection->setPageSize($searchCriteria->getPageSize());

        $activitys = [];
        /** @var ActivityInterface $activity */
        foreach ($activityCollection->getItems() as $activity) {
            $activitys[] = $this->getById($activity->getId());
        }

        $searchResults->setItems($activitys);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $activityCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $activityCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $activityCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $activityCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $activityCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $activityCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
