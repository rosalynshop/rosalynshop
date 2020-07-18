<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Repository;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Api\CommentRepositoryInterface;
use Amasty\AdvancedReview\Model\CommentFactory;
use Amasty\AdvancedReview\Model\ResourceModel\Comment as CommentResource;
use Amasty\AdvancedReview\Model\ResourceModel\Comment\Collection;
use Amasty\AdvancedReview\Model\ResourceModel\Comment\CollectionFactory;
use Amasty\AdvancedReview\Model\ResourceModel\Comment\Grid;
use Amasty\AdvancedReview\Model\Sources\CommentStatus;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CommentRepository implements CommentRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CommentFactory
     */
    private $commentFactory;

    /**
     * @var CommentResource
     */
    private $commentResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $comments = [];

    /**
     * @var CollectionFactory
     */
    private $commentCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        CommentFactory $commentFactory,
        CommentResource $commentResource,
        CollectionFactory $commentCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->commentFactory = $commentFactory;
        $this->commentResource = $commentResource;
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritdoc
     */
    public function save(CommentInterface $comment)
    {
        try {
            if ($comment->getId()) {
                $comment = $this->getById($comment->getId())->addData($comment->getData());
            }
            $this->commentResource->save($comment);
            unset($this->comments[$comment->getId()]);
        } catch (\Exception $e) {
            if ($comment->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save comment with ID %1. Error: %2',
                        [$comment->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new comment. Error: %1', $e->getMessage()));
        }

        return $comment;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->comments[$id])) {
            /** @var \Amasty\AdvancedReview\Model\Comment $comment */
            $comment = $this->commentFactory->create();
            $this->commentResource->load($comment, $id);
            if (!$comment->getId()) {
                throw new NoSuchEntityException(__('Comment with specified ID "%1" not found.', $id));
            }
            $this->comments[$id] = $comment;
        }

        return $this->comments[$id];
    }

    /**
     * @inheritdoc
     */
    public function delete(CommentInterface $comment)
    {
        try {
            $this->commentResource->delete($comment);
            unset($this->comments[$comment->getId()]);
        } catch (\Exception $e) {
            if ($comment->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove comment with ID %1. Error: %2',
                        [$comment->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove comment. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $commentModel = $this->getById($id);
        $this->delete($commentModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\AdvancedReview\Model\ResourceModel\Comment\Collection $commentCollection */
        $commentCollection = $this->commentCollectionFactory->create();
        
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $commentCollection);
        }
        
        $searchResults->setTotalCount($commentCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $commentCollection);
        }
        
        $commentCollection->setCurPage($searchCriteria->getCurrentPage());
        $commentCollection->setPageSize($searchCriteria->getPageSize());
        
        $comments = [];
        /** @var CommentInterface $comment */
        foreach ($commentCollection->getItems() as $comment) {
            $comments[] = $this->getById($comment->getId());
        }
        
        $searchResults->setItems($comments);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $commentCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $commentCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $commentCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
    * Helper function that adds a SortOrder to the collection.
    *
    * @param SortOrder[] $sortOrders
    * @param Collection  $commentCollection
    *
    * @return void
    */
    private function addOrderToCollection($sortOrders, Collection $commentCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $commentCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getComment()
    {
        return $this->commentFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getListByReviewId($reviewId)
    {
        $sortOrder = $this->sortOrderBuilder->setField(CommentInterface::CREATED_AT)->setAscendingDirection()
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            CommentInterface::REVIEW_ID,
            $reviewId
        )->addFilter(
            CommentInterface::STATUS,
            CommentStatus::STATUS_APPROVED
        )->setSortOrders(
            [$sortOrder]
        )->create();
        return $this->getList($searchCriteria);
    }
}
