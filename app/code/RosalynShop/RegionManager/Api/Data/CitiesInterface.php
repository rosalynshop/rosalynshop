<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Api\Data;

/**
 * Interface CitiesInterface
 * @package RosalynShop\RegionManager\Api\Data
 */
interface CitiesInterface
{
    const ID            = 'entity_id';
    const STATES_NAME   = 'states_name';
    const CITIES_NAME   = 'cities_name';

    /**
     * Get entity id.
     *
     * @return int
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getStatesName();

    /**
     * @return mixed
     */
    public function getCitiesName();

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @param $states_name
     * @return mixed
     */
    public function setStatesName($states_name);

    /**
     * @param $cities_name
     * @return mixed
     */
    public function setCitiesName($cities_name);

}