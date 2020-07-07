<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Block\Adminhtml\System\Wards\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

/**
 * Class ToWardsButton
 * @package RosalynShop\RegionManager\Block\Adminhtml\System\Wards\Button
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