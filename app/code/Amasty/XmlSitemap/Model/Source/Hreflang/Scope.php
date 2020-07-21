<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Source\Hreflang;

class Scope implements \Magento\Framework\Option\ArrayInterface
{
    const SCOPE_GLOBAL = '0';
    const SCOPE_WEBSITE = '1';

    /**
     * @return array
     */
    public function toOptionArray()    {
        return [
            ['value' => self::SCOPE_GLOBAL, 'label' => __('Global')],
            ['value' => self::SCOPE_WEBSITE, 'label' => __('Website')]
        ];
    }
}
