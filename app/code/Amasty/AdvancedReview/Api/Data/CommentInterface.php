<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Api\Data;

interface CommentInterface
{
    const TABLE = 'amasty_advanced_review_comments';
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const REVIEW_ID = 'review_id';
    const STORE_ID = 'store_id';
    const STATUS = 'status';
    const CUSTOMER_ID = 'customer_id';
    const MESSAGE = 'message';
    const NICKNAME = 'nickname';
    const EMAIL = 'email';
    const SESSION_ID = 'session_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getReviewId();

    /**
     * @param int $reviewId
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setReviewId($reviewId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setStatus($status);

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int|null $customerId
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return string|null
     */
    public function getMessage();

    /**
     * @param string|null $message
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setMessage($message);

    /**
     * @return string|null
     */
    public function getNickname();

    /**
     * @param string|null $nickname
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setNickname($nickname);

    /**
     * @return string|null
     */
    public function getEmail();

    /**
     * @param string|null $email
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setEmail($email);

    /**
     * @return string|null
     */
    public function getSessionId();

    /**
     * @param string|null $sessionId
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setSessionId($sessionId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     *
     * @return \Amasty\AdvancedReview\Api\Data\CommentInterface
     */
    public function setUpdatedAt($updatedAt);
}
