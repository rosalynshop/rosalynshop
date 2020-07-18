<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Api;

/**
 * Interface ImagesRepositoryInterface
 * @api
 */
interface ImagesRepositoryInterface
{
    /**
     * @param \Amasty\AdvancedReview\Api\Data\ImagesInterface $image
     * @return \Amasty\AdvancedReview\Api\Data\ImagesInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Amasty\AdvancedReview\Api\Data\ImagesInterface $image);

    /**
     * @param int $imageId
     * @return \Amasty\AdvancedReview\Api\Data\ImagesInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($imageId);

    /**
     * @param Data\ImagesInterface $image
     * @return mixed
     */
    public function delete(\Amasty\AdvancedReview\Api\Data\ImagesInterface $image);

    /**
     * @param int $imageId
     * @return bool
     */
    public function deleteById($imageId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param $reviewId
     * @return bool
     */
    public function deleteByReviewId($reviewId);

    /**
     * @return \Amasty\AdvancedReview\Model\Images
     */
    public function getImageModel();

    /**
     * @return array
     */
    public function getImageKeys();
}
