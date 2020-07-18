<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Api;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;

/**
 * @api
 */
interface ReminderRepositoryInterface
{
    /**
     * Save
     *
     * @param ReminderInterface $reminder
     * @return ReminderInterface
     */
    public function save(ReminderInterface $reminder);

    /**
     * Get by id
     *
     * @param int $entityId
     * @return ReminderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * Delete
     *
     * @param ReminderInterface $reminder
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ReminderInterface $reminder);

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
