<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Repository;

use Amasty\Fpc\Api\Data\ActivityInterface;
use Amasty\Fpc\Api\ActivityRepositoryInterface;
use Amasty\Fpc\Model\ActivityFactory;
use Amasty\Fpc\Model\ResourceModel\Activity as ActivityResource;
use Amasty\Fpc\Model\ResourceModel\Activity\CollectionFactory;
use Amasty\Fpc\Model\ResourceModel\Activity\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ActivityRepository implements ActivityRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ActivityFactory
     */
    private $activityFactory;

    /**
     * @var ActivityResource
     */
    private $activityResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $activitys;

    /**
     * @var CollectionFactory
     */
    private $activityCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        ActivityFactory $activityFactory,
        ActivityResource $activityResource,
        CollectionFactory $activityCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->activityFactory = $activityFactory;
        $this->activityResource = $activityResource;
        $this->activityCollectionFactory = $activityCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(ActivityInterface $activity)
    {
        try {
            if ($activityId = $this->activityResource->matchUrl($activity->getUrl(), $activity->getMobile())) {
                $activity = $this->getById($activityId);
                $activity->setRate($activity->getRate() + 1);
            }
            $this->activityResource->save($activity);
            unset($this->activitys[$activity->getId()]);
        } catch (\Exception $e) {
            if ($activity->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save activity with ID %1. Error: %2',
                        [$activity->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new activity. Error: %1', $e->getMessage()));
        }

        return $activity;
    }

    /**
     * @param sting $url
     *
     * @return bool
     */
    public function getByUrl($url)
    {
        $activity = $this->activityFactory->create();
        $this->activityResource->load($activity, $url, 'url');

        return $activity;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->activitys[$id])) {
            /** @var \Amasty\Fpc\Model\Activity $activity */
            $activity = $this->activityFactory->create();
            $this->activityResource->load($activity, $id);
            if (!$activity->getId()) {
                throw new NoSuchEntityException(__('Activity with specified ID "%1" not found.', $id));
            }
            $this->activitys[$id] = $activity;
        }

        return $this->activitys[$id];
    }

    /**
     * @inheritdoc
     */
    public function delete(ActivityInterface $activity)
    {
        try {
            $this->activityResource->delete($activity);
            unset($this->activitys[$activity->getId()]);
        } catch (\Exception $e) {
            if ($activity->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove activity with ID %1. Error: %2',
                        [$activity->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove activity. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $activityModel = $this->getById($id);
        $this->delete($activityModel);

        return true;
    }

    /**
     * @inheritdoc
     */
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
