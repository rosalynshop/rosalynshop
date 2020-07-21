<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Source\Hreflang;

use Magento\Directory\Model\Config\Source\Country\Full as CountrySource;

class Country implements \Magento\Framework\Option\ArrayInterface
{
    const DONT_ADD = '0';
    const DEFAULT_VALUE = '1';

    /**
     * @var CountrySource
     */
    private $countrySource;

    public function __construct(CountrySource $countrySource)
    {
        $this->countrySource = $countrySource;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => self::DONT_ADD, 'label' => __("Don't Add")],
            ['value' => self::DEFAULT_VALUE, 'label' => __('From Current Store Default Country')]
        ];

        $countries = array_map(
            function($row) {
                $row['label'] .= ' (' . $row['value'] . ')';
                return $row;
            },
            $this->countrySource->toOptionArray()
        );

        return $options + $countries;
    }
}
