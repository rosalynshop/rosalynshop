<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Plugin\Block\Address;

/**
 * Class Edit
 * @package Zemi\RegionManager\Plugin\Block\Address
 */
class Edit
{
    /**
     * @param \Magento\Customer\Block\Address\Edit $subject
     * @param $result
     * @return string
     */
    public function afterGetTemplate(
        \Magento\Customer\Block\Address\Edit $subject,
        $result
    ) {
        return 'Zemi_RegionManager::address/edit.phtml';
    }
}