<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Model;

use Magento\Framework\Model\AbstractModel;
use RosalynShop\RegionManager\Api\Data\StatesInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class States
 * @package RosalynShop\RegionManager\Model
 */
class States extends AbstractModel implements StatesInterface, IdentityInterface
{
    const CACHE_TAG = 'regionmanager_states';

    protected $_cacheTag = 'regionmanager_states';

    protected $_eventPrefix = 'regionmanager_states';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('RosalynShop\RegionManager\Model\ResourceModel\States');
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
    public function getStatesName()
    {
        return $this->getData(self::STATES_NAME);
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
     * @param $states_name
     * @return $this|mixed
     */
    public function setStatesName($states_name)
    {
        return $this->setData(self::STATES_NAME, $states_name);
    }
}