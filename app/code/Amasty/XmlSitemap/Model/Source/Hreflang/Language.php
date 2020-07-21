<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Source\Hreflang;


class Language implements \Magento\Framework\Option\ArrayInterface
{
    const DEFAULT_VALUE = '1';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [['value' => self::DEFAULT_VALUE, 'label' => __('From Current Store Locale')]];
        foreach(\Zend_Locale_Data_Translation::$languageTranslation as $language => $code) {
            $options[] = ['value' => $code, 'label' => $language . ' (' . $code . ')'];
        }

        return $options;
    }
}
