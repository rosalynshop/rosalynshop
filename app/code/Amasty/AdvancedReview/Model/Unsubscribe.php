<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model;

use Amasty\AdvancedReview\Api\Data\UnsubscribeInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Unsubscribe
 * @package Amasty\AdvancedReview\Model
 */
class Unsubscribe extends AbstractModel implements UnsubscribeInterface
{
    public function _construct()
    {
        $this->_init(\Amasty\AdvancedReview\Model\ResourceModel\Vote::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(Unsubscribe::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->setData(Unsubscribe::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnsubscribedAt()
    {
        return $this->_getData(Unsubscribe::UNSUBSCRIBED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUnsubscribedAt($unsubscribedAt)
    {
        $this->setData(Unsubscribe::UNSUBSCRIBED_AT, $unsubscribedAt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->_getData(Unsubscribe::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        $this->setData(Unsubscribe::EMAIL, $email);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsComment()
    {
        return $this->_getData(Unsubscribe::IS_COMMENT);
    }

    /**
     * @inheritdoc
     */
    public function setIsComment($isComment)
    {
        $this->setData(Unsubscribe::IS_COMMENT, $isComment);

        return $this;
    }
}
