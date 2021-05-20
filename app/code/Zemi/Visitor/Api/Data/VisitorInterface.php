<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\Visitor\Api\Data;

/**
 * Interface VisitorCustomer
 * @package Zemi\Visitor\Api\Data
 */
interface VisitorInterface
{
    const ID              = 'entity_id';
    const IP_ADDRESS      = 'ip_address';
    const PRODUCT_ID      = 'product_id';
    const CUSTOMER_ID     = 'customer_id';
    const DATE_TIME       = 'date_time';

    /**
     * Get entity id.
     *
     * @return int
     */
    public function getId();

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getIpAddress();

    /**
     * @return mixed
     */
    public function setIpAddress();

    /**
     * @return mixed
     */
    public function getProductId();

    /**
     * @param $productId
     * @return mixed
     */
    public function setProductId($productId);

    /**
     * @return mixed
     */
    public function getCustomerId();

    /**
     * @param $customerId
     * @return mixed
     */
    public function setCustomerId($customerId);

    /**
     * @return mixed
     */
    public function getDateTime();

    /**
     * @param $dateTime
     * @return mixed
     */
    public function setDateTime($dateTime);
}
