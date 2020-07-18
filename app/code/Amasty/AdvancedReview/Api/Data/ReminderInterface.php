<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Api\Data;

interface ReminderInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const ORDER_ID = 'order_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STATUS = 'status';
    const SEND_DATE = 'send_date';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\AdvancedReview\Api\Data\ReminderInterface
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     *
     * @return \Amasty\AdvancedReview\Api\Data\ReminderInterface
     */
    public function setOrderId($orderId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return \Amasty\AdvancedReview\Api\Data\ReminderInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     *
     * @return \Amasty\AdvancedReview\Api\Data\ReminderInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string
     */
    public function getSendDate();

    /**
     * @param string $sendDate
     *
     * @return \Amasty\AdvancedReview\Api\Data\ReminderInterface
     */
    public function setSendDate($sendDate);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\AdvancedReview\Api\Data\ReminderInterface
     */
    public function setStatus($status);
}
