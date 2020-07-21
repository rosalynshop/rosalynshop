<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Model\Source;

class Breadcrumb implements \Magento\Framework\Option\ArrayInterface
{
    const CURRENT_URL = 'current_url';
    const LAST_VISITED = 'last_visited';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CURRENT_URL,
                'label' => __('Current URL')
            ],
            [
                'value' => self::LAST_VISITED,
                'label' => __('Last Visited Category')
            ]
        ];
    }
}
