<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Repository;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Api\ReminderRepositoryInterface;
use Amasty\AdvancedReview\Model\ResourceModel\Reminder as ReminderResource;
use Amasty\AdvancedReview\Model\ResourceModel\Reminder\CollectionFactory;
use Amasty\AdvancedReview\Model\ResourceModel\Reminder\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Amasty\AdvancedReview\Model\ReminderFactory;

/**
 * Class ReminderRepository
 * @package Amasty\AdvancedReview\Model\Repository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReminderRepository implements ReminderRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ReminderFactory
     */
    protected $reminderFactory;

    /**
     * @var ReminderResource
     */
    private $reminderResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $reminders;

    /**
     * @var CollectionFactory
     */
    private $reminderCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        ReminderFactory $reminderFactory,
        ReminderResource $reminderResource,
        CollectionFactory $reminderCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->reminderFactory = $reminderFactory;
        $this->reminderResource = $reminderResource;
        $this->reminderCollectionFactory = $reminderCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(ReminderInterface $reminder)
    {
        try {
            if ($reminder->getEntityId()) {
                $reminder = $this->getById($reminder->getEntityId())->addData($reminder->getData());
            }
            $this->reminderResource->save($reminder);
            unset($this->reminders[$reminder->getEntityId()]);
        } catch (\Exception $e) {
            if ($reminder->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save reminder item with ID %1. Error: %2',
                        [$reminder->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new reminder item. Error: %1', $e->getMessage()));
        }

        return $reminder;
    }

    /**
     * @inheritdoc
     */
    public function getById($entityId)
    {
        if (!isset($this->reminders[$entityId])) {
            /** @var \Amasty\AdvancedReview\Model\Reminder $reminder */
            $reminder = $this->reminderFactory->create();
            $this->reminderResource->load($reminder, $entityId);
            if (!$reminder->getEntityId()) {
                throw new NoSuchEntityException(__('Reminder item with specified ID "%1" not found.', $entityId));
            }
            $this->reminders[$entityId] = $reminder;
        }

        return $this->reminders[$entityId];
    }

    /**
     * @inheritdoc
     */
    public function delete(ReminderInterface $reminder)
    {
        try {
            $this->reminderResource->delete($reminder);
            unset($this->reminders[$reminder->getEntityId()]);
        } catch (\Exception $e) {
            if ($reminder->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove reminder item with ID %1. Error: %2',
                        [$reminder->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove reminder item. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($entityId)
    {
        $reminderModel = $this->getById($entityId);
        $this->delete($reminderModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\AdvancedReview\Model\ResourceModel\Reminder\Collection $reminderCollection */
        $reminderCollection = $this->reminderCollectionFactory->create();
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $reminderCollection);
        }

        $searchResults->setTotalCount($reminderCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $reminderCollection);
        }

        $reminderCollection->setCurPage($searchCriteria->getCurrentPage());
        $reminderCollection->setPageSize($searchCriteria->getPageSize());
        $reminders = [];

        /** @var ReminderInterface $reminder */
        foreach ($reminderCollection->getItems() as $reminder) {
            $reminders[] = $this->getById($reminder->getId());
        }
        $searchResults->setItems($reminders);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $reminderCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $reminderCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $reminderCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $reminderCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $reminderCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $reminderCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
