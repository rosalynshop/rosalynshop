<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Amasty\Fpc\Api\Data\ActivityInterface;

class Activity extends AbstractModel implements ActivityInterface, IdentityInterface
{
    const CACHE_TAG = 'amasty_fpc_activity';

    public function _construct()
    {
        $this->_init(ResourceModel\Activity::class);
    }

    /**
     * @inheritdoc
     */
    public function getRate()
    {
        return $this->_getData(ActivityInterface::RATE);
    }

    /**
     * @inheritdoc
     */
    public function setRate($rate)
    {
        return $this->setData(ActivityInterface::RATE, $rate);
    }

    /**
     * @inheritdoc
     */
    public function getStore()
    {
        return $this->_getData(ActivityInterface::STORE);
    }

    /**
     * @inheritdoc
     */
    public function setStore($store)
    {
        return $this->setData(ActivityInterface::STORE, $store);
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->_getData(ActivityInterface::URL);
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        return $this->setData(ActivityInterface::URL, $url);
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->_getData(ActivityInterface::CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setCurrency($currency)
    {
        return $this->setData(ActivityInterface::CURRENCY, $currency);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroup()
    {
        return $this->_getData(ActivityInterface::CUSTOMER_GROUP);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerGroup($customerGroup)
    {
        return $this->setData(ActivityInterface::CUSTOMER_GROUP, $customerGroup);
    }

    /**
     * @inheritdoc
     */
    public function getMobile()
    {
        return $this->_getData(ActivityInterface::MOBILE);
    }

    /**
     * @inheritdoc
     */
    public function setMobile($mobile)
    {
        return $this->setData(ActivityInterface::MOBILE, $mobile);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(ActivityInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(ActivityInterface::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->_getData(ActivityInterface::DATE);
    }

    /**
     * @inheritdoc
     */
    public function setDate($date)
    {
        return $this->setData(ActivityInterface::DATE, $date);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }

    /**
     * Get list of cache tags applied to model object.
     *
     * @return array
     */
    public function getCacheTags()
    {
        $tags = parent::getCacheTags();
        if (!$tags) {
            $tags = [];
        }
        return $tags + $this->getIdentities();
    }
}
