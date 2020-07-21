<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class Breadcrumbs implements ArrayInterface
{
    const TYPE_LONG = 0;
    const TYPE_SHORT = 1;

    public function toOptionArray()
    {
        return [
            self::TYPE_LONG => __('Default (Long)'),
            self::TYPE_SHORT => __('Short'),
        ];
    }
}
