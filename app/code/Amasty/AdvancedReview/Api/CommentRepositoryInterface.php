<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Api;

/**
 * @api
 */
interface CommentRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\AdvancedReview\Api\Data\CommentInterface $comment
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function save(\Amasty\AdvancedReview\Api\Data\CommentInterface $comment);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\AdvancedReview\Api\Data\CommentInterface $comment
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\AdvancedReview\Api\Data\CommentInterface $comment);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function getComment();

    /**
     * @param int $reviewId
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getListByReviewId($reviewId);
}
