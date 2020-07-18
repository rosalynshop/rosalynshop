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
interface UnsubscribeRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface $unsubscribe
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     */
    public function save(\Amasty\AdvancedReview\Api\Data\UnsubscribeInterface $unsubscribe);

    /**
     * Get by id
     *
     * @param int $entityId
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * Delete
     *
     * @param \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface $unsubscribe
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\AdvancedReview\Api\Data\UnsubscribeInterface $unsubscribe);

    /**
     * Delete by id
     *
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
