<?php

namespace RosalynShop\ConvertTextVN\Plugin\Product;

class Url
{
    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Url constructor.
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \RosalynShop\ConvertTextVN\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\Filter\FilterManager $filter,
        \RosalynShop\ConvertTextVN\Helper\Data $helperData
    ) {
        $this->filter = $filter;
        $this->helperData = $helperData;
    }

    public function aroundFormatUrlKey(
        \Magento\Catalog\Model\Product\Url $subject,
        \Closure $proceed, $str
    ) {
        return $this->filter->translitUrl($this->helperData->convertTextVN($str));
    }
}