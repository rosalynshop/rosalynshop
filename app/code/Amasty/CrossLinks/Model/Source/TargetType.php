<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model\Source;

/**
 * Class TargetType
 * @package Amasty\CrossLinks\Model\Source
 */
class TargetType implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_SELF = '_self';
    const TYPE_BLANK = '_blank';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_SELF, 'label' => __('Self')],
            ['value' => self::TYPE_BLANK, 'label' => __('Blank')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::TYPE_SELF => __('Self'), self::TYPE_BLANK => __('Blank')];
    }
}
