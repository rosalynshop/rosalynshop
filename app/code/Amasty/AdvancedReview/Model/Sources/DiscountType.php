<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Option\ArrayInterface;
use \Magento\SalesRule\Model\Rule;

class DiscountType implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => Rule::BY_PERCENT_ACTION,
                'label' => __('Percent of product price discount')
            ],
            [
                'value' => Rule::BY_FIXED_ACTION,
                'label' => __('Fixed amount discount')
            ],
            [
                'value' => Rule::CART_FIXED_ACTION,
                'label' => __('Fixed amount discount for whole cart')
            ]
        ];

        return $options;
    }
}
