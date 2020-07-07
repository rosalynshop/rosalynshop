<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use RosalynShop\RegionManager\Api\Data\WardsInterface;

/**
 * Class Wards
 * @package RosalynShop\RegionManager\Model\ResourceModel
 */
class Wards extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('regionmanager_wards', WardsInterface::ID);
    }
}