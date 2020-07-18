<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Api;

/**
 * Interface VoteRepositoryInterface
 * @api
 */
interface VoteRepositoryInterface
{
    /**
     * @param \Amasty\AdvancedReview\Api\Data\VoteInterface $vote
     * @return \Amasty\AdvancedReview\Api\Data\VoteInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Amasty\AdvancedReview\Api\Data\VoteInterface $vote);

    /**
     * @param int $voteId
     * @return \Amasty\AdvancedReview\Api\Data\VoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($voteId);

    /**
     * @param $reviewId
     * @param $ip
     * @return \Amasty\AdvancedReview\Model\Vote|\Magento\Framework\DataObject
     */
    public function getByIdAndIp($reviewId, $ip);

    /**
     * @param Data\VoteInterface $vote
     * @return mixed
     */
    public function delete(\Amasty\AdvancedReview\Api\Data\VoteInterface $vote);

    /**
     * @param int $voteId
     *
     * @return boolean
     */
    public function deleteById($voteId);

    /**
     * @return \Amasty\AdvancedReview\Model\Vote
     */
    public function getVoteModel();

    /**
     * @return array
     */
    public function getVoteIpKeys();
}
