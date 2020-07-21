<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Api;

interface FlushesLogRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Fpc\Api\Data\FlushesLogInterface $flushesLog
     *
     * @return \Amasty\Fpc\Api\Data\FlushesLogInterface
     */
    public function save(\Amasty\Fpc\Api\Data\FlushesLogInterface $flushesLog);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\Fpc\Api\Data\FlushesLogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\Fpc\Api\Data\FlushesLogInterface $flushesLog
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Fpc\Api\Data\FlushesLogInterface $flushesLog);

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
}
