<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Plugin\Model\Attribute\Data;

/**
 * Class Postcode
 * @package RosalynShop\RegionManager\Plugin\Model\Attribute\Data
 */
class Postcode
{
    public function aroundValidateValue(
        \Magento\Customer\Model\Attribute\Data\Postcode $postcode, callable $proceed
    ){
        return true;
    }
}