<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Model;

use Magento\Framework\Model\AbstractModel;
use Zemi\RegionManager\Api\Data\WardsInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Wards
 * @package Zemi\RegionManager\Model
 */
class Wards extends AbstractModel implements WardsInterface, IdentityInterface
{
    const CACHE_TAG = 'regionmanager_wards';

    protected $_cacheTag = 'regionmanager_wards';

    protected $_eventPrefix = 'regionmanager_wards';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Zemi\RegionManager\Model\ResourceModel\Wards');
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
     * @return mixed
     */
    public function getCitiesName()
    {
        return $this->getData(self::CITIES_NAME);
    }

    /**
     * @return mixed
     */
    public function getWardsName()
    {
        return $this->getData(self::WARDS_NAME);
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
     * @param $citiesName
     * @return $this|mixed
     */
    public function setCitiesName($citiesName)
    {
        return $this->setData(self::CITIES_NAME, $citiesName);
    }

    /**
     * @param $wardsName
     * @return $this|mixed
     */
    public function setWardsName($wardsName)
    {
        return $this->setData(self::WARDS_NAME, $wardsName);
    }
}