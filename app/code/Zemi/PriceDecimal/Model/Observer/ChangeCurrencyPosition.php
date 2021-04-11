<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\PriceDecimal\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class ChangeCurrencyPosition implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $currencyOptions->setData('position', \Magento\Framework\Currency::RIGHT);
        return $this;
    }
}
