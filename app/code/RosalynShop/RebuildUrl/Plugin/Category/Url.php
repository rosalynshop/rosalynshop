<?php

namespace RosalynShop\RebuildUrl\Plugin\Category;

class Url
{
    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Url constructor.
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \RosalynShop\RebuildUrl\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\Filter\FilterManager $filter,
        \RosalynShop\RebuildUrl\Helper\Data $helperData
    ) {
        $this->filter = $filter;
        $this->helperData = $helperData;
    }

    public function aroundFormatUrlKey(
        \Magento\Catalog\Model\Category $subject,
        \Closure $proceed, $str
    ) {
        return $this->filter->translitUrl($this->helperData->convertTextVN($str));
    }
}