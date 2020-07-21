<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Source\Hreflang;


class CmsRelation implements \Magento\Framework\Option\ArrayInterface
{
    const ID = 'page_id';
    const UUID = 'amseo-uuid';
    const IDENTIFIER = 'identifier';

    /**
     * @return array
     */
    public function toOptionArray()    {
        return [
            ['value' => self::ID, 'label' => __('By ID')],
            ['value' => self::UUID, 'label' => __('By Hreflang UUID')],
            ['value' => self::IDENTIFIER, 'label' => __('By URL Key (Page Identifier)')]
        ];
    }
}
