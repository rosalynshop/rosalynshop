<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Block\Adminhtml\System\Wards\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

/**
 * Class ToWardsButton
 * @package Zemi\RegionManager\Block\Adminhtml\System\Wards\Button
 */
class ToWardsButton extends Generic
{
    public function getButtonData()
    {
        $url = $this->getUrl('regionmanager/wards/edit');
        return [
            'label' => __('Add Wards'),
            'on_click' => "window.location='{$url}';",
            'sort_order' => 100
        ];
    }
}