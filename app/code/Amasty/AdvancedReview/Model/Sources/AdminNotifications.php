<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Sources;

class AdminNotifications
{
    const DISABLED = 0;

    const INSTANTLY = 2;

    const DAILY = 1;

    /**
     * @return array
     */
    public static function toOptionArray()
    {
        $options = [
            [
                'value' => self::DISABLED,
                'label' => __('No')
            ],
            [
                'value' => self::INSTANTLY,
                'label' => __('Yes (Instantly)')
            ],
            [
                'value' => self::DAILY,
                'label' => __('Yes (Daily)')
            ]
        ];

        return $options;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public static function getLabel($value)
    {
        foreach (self::toOptionArray() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return '';
    }
}
