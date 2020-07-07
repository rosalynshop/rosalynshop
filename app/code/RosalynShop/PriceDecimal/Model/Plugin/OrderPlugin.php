<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Thanhthuy Shop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\PriceDecimal\Model\Plugin;

class OrderPlugin extends PriceFormatPluginAbstract
{
    /**
     * @param \Magento\Sales\Model\Order $subject
     * @param array ...$args
     * @return array
     */
    public function beforeFormatPricePrecision(
        \Magento\Sales\Model\Order $subject,
        ...$args
    ) {
        //is enabled
        if ($this->getConfig()->isEnable()) {
            //change the precision
            $args[1] = $this->getPricePrecision();
        }

        return $args;
    }
}
