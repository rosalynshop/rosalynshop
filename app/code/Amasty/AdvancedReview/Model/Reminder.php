<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Reminder
 * @package Amasty\AdvancedReview\Model
 */
class Reminder extends AbstractModel implements ReminderInterface
{
    public function _construct()
    {
        $this->_init(\Amasty\AdvancedReview\Model\ResourceModel\Reminder::class);
    }
    
    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(ReminderInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->setData(ReminderInterface::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->_getData(ReminderInterface::ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($orderId)
    {
        $this->setData(ReminderInterface::ORDER_ID, $orderId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(ReminderInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(ReminderInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(ReminderInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(ReminderInterface::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(ReminderInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(ReminderInterface::STATUS, $status);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSendDate()
    {
        return $this->_getData(ReminderInterface::SEND_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setSendDate($sendDate)
    {
        $this->setData(ReminderInterface::SEND_DATE, $sendDate);

        return $this;
    }
}
