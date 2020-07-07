<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\PriceDecimal\Model\Plugin;

/**
 * Class Currency
 * @package RosalynShop\PriceDecimal\Model\Plugin
 */
class Currency extends PriceFormatPluginAbstract
{

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Framework\CurrencyInterface $subject
     * @param array                                ...$args
     *
     * @return array
     */
    public function beforeToCurrency(
        \RosalynShop\PriceDecimal\Model\Currency $subject,
        ...$arguments
    ) {
        if ($this->getConfig()->isEnable()) {
            $arguments[1]['precision'] = $subject->getPricePrecision();
        }
        return $arguments;
    }
}
