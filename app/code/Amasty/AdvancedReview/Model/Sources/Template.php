<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Sources;

/**
 * Class Template
 * @package Amasty\AdvancedReview\Model\Sources
 */
class Template implements \Magento\Framework\Option\ArrayInterface
{
    const DEFAULT_TEMPLATE = 'Amasty_AdvancedReview::widget/review/content/main.phtml';
    const LIST_DEFAULT = 'Amasty_AdvancedReview::widget/review/sidebar/sidebar.phtml';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::DEFAULT_TEMPLATE,
                'label' => __('Review Grid Template')
            ],
            [
                'value' => self::LIST_DEFAULT,
                'label' => __('Products Images and Names Template (vert.)')
            ]
        ];

        return $options;
    }
}
