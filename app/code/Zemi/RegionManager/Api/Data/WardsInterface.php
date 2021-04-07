<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Api\Data;

/**
 * Interface CitiesInterface
 * @package Zemi\RegionManager\Api\Data
 */
interface WardsInterface
{
    const ID            = 'entity_id';
    const CITIES_NAME   = 'cities_name';
    const WARDS_NAME    = 'wards_name';

    /**
     * Get entity id.
     *
     * @return int
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getCitiesName();

    /**
     * @return mixed
     */
    public function getWardsName();

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @param $citiesName
     * @return mixed
     */
    public function setCitiesName($citiesName);

    /**
     * @param $wardsName
     * @return mixed
     */
    public function setWardsName($wardsName);
}