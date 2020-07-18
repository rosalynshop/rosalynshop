<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Api\Data;

interface VoteInterface
{
    const VOTE_ID = 'vote_id';
    const REVIEW_ID = 'review_id';
    const TYPE = 'type';
    const IP = 'ip';

    /**
     * Returns vote id field
     *
     * @return int|null
     */
    public function getVoteId();

    /**
     * @param int $voteId
     *
     * @return $this
     */
    public function setVoteId($voteId);

    /**
     * Returns review id field
     *
     * @return int|null
     */
    public function getReviewId();

    /**
     * @param int $reviewId
     *
     * @return $this
     */
    public function setReviewId($reviewId);

    /**
     * Returns vote path
     *
     * @return int|null
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * Returns vote path
     *
     * @return string|null
     */
    public function getIp();

    /**
     * @param string $ip
     *
     * @return $this
     */
    public function setIp($ip);
}
