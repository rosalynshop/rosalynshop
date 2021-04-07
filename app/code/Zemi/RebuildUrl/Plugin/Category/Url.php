<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RebuildUrl\Plugin\Category;

class Url
{
    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Url constructor.
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \Zemi\RebuildUrl\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\Filter\FilterManager $filter,
        \Zemi\RebuildUrl\Helper\Data $helperData
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