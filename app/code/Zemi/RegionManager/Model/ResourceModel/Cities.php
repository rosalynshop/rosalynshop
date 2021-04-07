<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Zemi\RegionManager\Api\Data\CitiesInterface;

/**
 * Class Cities
 * @package Zemi\RegionManager\Model\ResourceModel
 */
class Cities extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('regionmanager_cities', CitiesInterface::ID);
    }
}