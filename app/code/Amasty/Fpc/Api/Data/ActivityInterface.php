<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Api\Data;

interface ActivityInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const RATE = 'rate';
    const STORE = 'store';
    const URL = 'url';
    const CURRENCY = 'currency';
    const CUSTOMER_GROUP = 'customer_group';
    const MOBILE = 'mobile';
    const STATUS = 'status';
    const PAGE_LOAD = 'page_load';
    const DATE = 'date';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\Fpc\Api\Data\ActivityInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getRate();

    /**
     * @param int $rate
     *
     * @return \Amasty\Fpc\Api\Data\ActivityInterface
     */
    public function setRate($rate);

    /**
     * @return int
     */
    public function getStore();

    /**
     * @param int $store
     *
     * @return \Amasty\Fpc\Api\Data\ActivityInterface
     */
    public function setStore($store);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     *
     * @return \Amasty\Fpc\Api\Data\ActivityInterface
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     *
     * @return \Amasty\Fpc\Api\Data\ActivityInterface
     */
    public function setCurrency($currency);

    /**
     * @return int
     */
    public function getCustomerGroup();

    /**
     * @param int $customerGroup
     *
     * @return \Amasty\Fpc\Api\Data\ActivityInterface
     */
    public function setCustomerGroup($customerGroup);

    /**
     * @return int
     */
    public function getMobile();

    /**
     * @param int $mobile
     *
     * @return \Amasty\Fpc\Api\Data\ActivityInterface
     */
    public function setMobile($mobile);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\Fpc\Api\Data\ActivityInterface
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getDate();

    /**
     * @param int $date
     *
     * @return \Amasty\Fpc\Api\Data\ActivityInterface
     */
    public function setDate($date);
}
