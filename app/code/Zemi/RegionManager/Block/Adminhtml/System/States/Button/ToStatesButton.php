<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Block\Adminhtml\System\States\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

/**
 * Class ToStatesButton
 * @package Zemi\RegionManager\Block\Adminhtml\System\States\Button
 */
class ToStatesButton extends Generic
{
    public function getButtonData()
    {
        $url = $this->getUrl('regionmanager/states/edit');
        return [
            'label' => __('Add new States'),
            'on_click' => "window.location='{$url}';",
            'sort_order' => 100
        ];
    }
}