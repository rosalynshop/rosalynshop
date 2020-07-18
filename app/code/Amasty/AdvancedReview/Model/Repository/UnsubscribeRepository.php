<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Repository;

use Amasty\AdvancedReview\Api\Data\UnsubscribeInterface;
use Amasty\AdvancedReview\Api\UnsubscribeRepositoryInterface;
use Amasty\AdvancedReview\Model\UnsubscribeFactory;
use Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe as UnsubscribeResource;
use Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe\CollectionFactory;
use Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

/**
 * Class UnsubscribeRepository
 * @package Amasty\AdvancedReview\Model\Repository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UnsubscribeRepository implements UnsubscribeRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var UnsubscribeFactory
     */
    private $unsubscribeFactory;

    /**
     * @var UnsubscribeResource
     */
    private $unsubscribeResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $unsubscribes;

    /**
     * @var CollectionFactory
     */
    private $unsubscribeCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        UnsubscribeFactory $unsubscribeFactory,
        UnsubscribeResource $unsubscribeResource,
        CollectionFactory $unsubscribeCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->unsubscribeFactory = $unsubscribeFactory;
        $this->unsubscribeResource = $unsubscribeResource;
        $this->unsubscribeCollectionFactory = $unsubscribeCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(UnsubscribeInterface $unsubscribe)
    {
        try {
            if ($unsubscribe->getEntityId()) {
                $unsubscribe = $this->getById($unsubscribe->getEntityId())->addData($unsubscribe->getData());
            }
            $this->unsubscribeResource->save($unsubscribe);
            unset($this->unsubscribes[$unsubscribe->getEntityId()]);
        } catch (\Exception $e) {
            if ($unsubscribe->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save unsubscribe with ID %1. Error: %2',
                        [$unsubscribe->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new unsubscribe. Error: %1', $e->getMessage()));
        }

        return $unsubscribe;
    }

    /**
     * @inheritdoc
     */
    public function getById($entityId)
    {
        if (!isset($this->unsubscribes[$entityId])) {
            /** @var \Amasty\AdvancedReview\Model\Unsubscribe $unsubscribe */
            $unsubscribe = $this->unsubscribeFactory->create();
            $this->unsubscribeResource->load($unsubscribe, $entityId);
            if (!$unsubscribe->getEntityId()) {
                throw new NoSuchEntityException(__('Unsubscribe with specified ID "%1" not found.', $entityId));
            }
            $this->unsubscribes[$entityId] = $unsubscribe;
        }

        return $this->unsubscribes[$entityId];
    }

    /**
     * @inheritdoc
     */
    public function delete(UnsubscribeInterface $unsubscribe)
    {
        try {
            $this->unsubscribeResource->delete($unsubscribe);
            unset($this->unsubscribes[$unsubscribe->getEntityId()]);
        } catch (\Exception $e) {
            if ($unsubscribe->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove unsubscribe with ID %1. Error: %2',
                        [$unsubscribe->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove unsubscribe. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($entityId)
    {
        $unsubscribeModel = $this->getById($entityId);
        $this->delete($unsubscribeModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe\Collection $unsubscribeCollection */
        $unsubscribeCollection = $this->unsubscribeCollectionFactory->create();
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $unsubscribeCollection);
        }
        $searchResults->setTotalCount($unsubscribeCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $unsubscribeCollection);
        }
        $unsubscribeCollection->setCurPage($searchCriteria->getCurrentPage());
        $unsubscribeCollection->setPageSize($searchCriteria->getPageSize());
        $unsubscribes = [];
        /** @var UnsubscribeInterface $unsubscribe */
        foreach ($unsubscribeCollection->getItems() as $unsubscribe) {
            $unsubscribes[] = $this->getById($unsubscribe->getId());
        }
        $searchResults->setItems($unsubscribes);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $unsubscribeCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $unsubscribeCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $unsubscribeCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $unsubscribeCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $unsubscribeCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $unsubscribeCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }
}
