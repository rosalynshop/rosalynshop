<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\Visitor\Model;

use Magento\Framework\Model\AbstractModel;
use Zemi\Visitor\Api\Data\VisitorInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Visitors
 * @package Zemi\Visitor\Model
 */
class Visitor extends AbstractModel implements VisitorInterface, IdentityInterface
{
    const CACHE_TAG = 'zm_visitor_customer';

    protected $_cacheTag = 'zm_visitor_customer';

    protected $_eventPrefix = 'zm_visitor_customer';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Zemi\Visitor\Model\ResourceModel\Visitor');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return int|mixed
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @param mixed $id
     * @return $this|mixed
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->getData(self::IP_ADDRESS);
    }

    /**
     * @return mixed
     */
    public function setIpAddress()
    {
        return $this->setData(self::IP_ADDRESS);
    }

    /**
     * @param $productId
     * @return array|mixed|null
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @param $productId
     * @return array|mixed|null
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @param $customerId
     * @return array|mixed|null
     */
    public function getCustomerId()
    {
        return $this->setData(self::CUSTOMER_ID);
    }

    /**
     * @param $customerId
     * @return array|mixed|null
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @param $customerId
     * @return array|mixed|null
     */
    public function getDateTime()
    {
        return $this->setData(self::DATE_TIME);
    }

    /**
     * @param $dateTime
     * @return array|mixed|null
     */
    public function setDateTime($dateTime)
    {
        return $this->setData(self::DATE_TIME, $dateTime);
    }
}
