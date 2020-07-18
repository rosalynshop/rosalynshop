<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\OptionSource\Widget;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 * @package Amasty\AdvancedReview\Model\OptionSource\Widget
 */
class Type implements ArrayInterface
{
    const RANDOM = 0;
    const RECENT = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::RECENT, 'label'=> __('Recent')],
            ['value' => self::RANDOM, 'label'=> __('Random')]
        ];
    }
}
