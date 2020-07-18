<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Model\ResourceModel\Comment as CommentResource;
use Magento\Framework\Model\AbstractModel;

class Comment extends AbstractModel implements CommentInterface
{
    public function _construct()
    {
        $this->_init(CommentResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getReviewId()
    {
        return $this->_getData(CommentInterface::REVIEW_ID);
    }

    /**
     * @inheritdoc
     */
    public function setReviewId($reviewId)
    {
        $this->setData(CommentInterface::REVIEW_ID, $reviewId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(CommentInterface::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->setData(CommentInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(CommentInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(CommentInterface::STATUS, $status);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->_getData(CommentInterface::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        $this->setData(CommentInterface::CUSTOMER_ID, $customerId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->_getData(CommentInterface::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessage($message)
    {
        $this->setData(CommentInterface::MESSAGE, $message);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNickname()
    {
        return $this->_getData(CommentInterface::NICKNAME);
    }

    /**
     * @inheritdoc
     */
    public function setNickname($nickname)
    {
        $this->setData(CommentInterface::NICKNAME, $nickname);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->_getData(CommentInterface::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        $this->setData(CommentInterface::EMAIL, $email);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSessionId()
    {
        return $this->_getData(CommentInterface::SESSION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSessionId($sessionId)
    {
        $this->setData(CommentInterface::SESSION_ID, $sessionId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(CommentInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(CommentInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(CommentInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(CommentInterface::UPDATED_AT, $updatedAt);

        return $this;
    }
}
