<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Sources;

/**
 * Class Recommend
 * @package Amasty\AdvancedReview\Model\Sources
 * phpcs:ignoreFile
 */
class Recommend
{
    const NOT_SELECTED = 0;
    const NOT_RECOMMENDED = 2;
    const RECOMMENDED = 1;

    /**
     * @return array
     */
    public static function toOptionArray()
    {
        $options = [
            [
                'value' => self::NOT_RECOMMENDED,
                'label' => __('No')
            ],
            [
                'value' => self::RECOMMENDED,
                'label' => __('Yes')
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
