<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Model\Source;

class By implements \Magento\Framework\Option\ArrayInterface
{
    const LEVEL_DEPTH = 'depth';
    const CHARACTER_NUMBER = 'number';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::LEVEL_DEPTH,
                'label' => __('URL Level Depth')
            ],
            [
                'value' => self::CHARACTER_NUMBER,
                'label' => __('Number of Characters')
            ]
        ];
    }
}
