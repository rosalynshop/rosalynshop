<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Model\Source\Product;

use Magento\Framework\Option\ArrayInterface;

class Offer implements ArrayInterface
{
    const CONFIGURABLE = 0;
    const LIST_OF_SIMPLES = 1;
    const AGGREGATE = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::CONFIGURABLE => __('Main Offer'),
            self::LIST_OF_SIMPLES => __('List of Associated Products Offers'),
            self::AGGREGATE => __('Aggregate Offer'),
        ];
    }
}
