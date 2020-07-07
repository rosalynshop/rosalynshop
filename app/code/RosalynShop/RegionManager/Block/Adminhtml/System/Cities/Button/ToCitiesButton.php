<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Block\Adminhtml\System\Cities\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

/**
 * Class ToCitiesButton
 * @package RosalynShop\RegionManager\Block\Adminhtml\System\Cities\Button
 */
class ToCitiesButton extends Generic
{
    public function getButtonData()
    {
        $url = $this->getUrl('regionmanager/cities/edit');
        return [
            'label' => __('Add cities'),
            'on_click' => "window.location='{$url}';",
            'sort_order' => 100
        ];
    }
}