<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Option\ArrayInterface;

class Frequency implements ArrayInterface
{
    const PER_PRODUCT = 0;
    const PER_CUSTOMER = 1;
    const PER_ORDER = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::PER_PRODUCT,
                'label' => __('Once per product')
            ],
            [
                'value' => self::PER_CUSTOMER,
                'label' => __('Once per customer')
            ],
            [
                'value' => self::PER_ORDER,
                'label' => __('Once per order')
            ]
        ];

        return $options;
    }
}
