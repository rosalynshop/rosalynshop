<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Plugin\Model\Attribute\Data;

/**
 * Class Postcode
 * @package Zemi\RegionManager\Plugin\Model\Attribute\Data
 */
class Postcode
{
    public function aroundValidateValue(
        \Magento\Customer\Model\Attribute\Data\Postcode $postcode, callable $proceed
    ){
        return true;
    }
}