<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model;

use Amasty\AdvancedReview\Api\Data\VoteInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Vote
 * @package Amasty\AdvancedReview\Model
 */
class Vote extends AbstractModel implements VoteInterface
{
    public function _construct()
    {
        $this->_init(\Amasty\AdvancedReview\Model\ResourceModel\Vote::class);
    }

    /**
     * Returns vote id field
     *
     * @return int|null
     */
    public function getVoteId()
    {
        return $this->getData(self::VOTE_ID);
    }

    /**
     * @param int $voteId
     *
     * @return $this
     */
    public function setVoteId($voteId)
    {
        $this->setData(self::VOTE_ID, $voteId);
        return $this;
    }

    /**
     * Returns review id field
     *
     * @return int|null
     */
    public function getReviewId()
    {
        return $this->getData(self::REVIEW_ID);
    }

    /**
     * @param int $reviewId
     *
     * @return $this
     */
    public function setReviewId($reviewId)
    {
        $this->setData(self::REVIEW_ID, $reviewId);
        return $this;
    }

    /**
     * Returns vote type
     *
     * @return int|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);
        return $this;
    }

    /**
     * Returns vote type
     *
     * @return string|null
     */
    public function getIp()
    {
        return $this->getData(self::IP);
    }

    /**
     * @param string $ip
     *
     * @return $this
     */
    public function setIp($ip)
    {
        $this->setData(self::IP, $ip);
        return $this;
    }
}
