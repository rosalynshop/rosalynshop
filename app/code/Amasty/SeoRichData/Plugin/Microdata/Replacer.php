<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Plugin\Microdata;

use Magento\Review\Block\Product\ReviewRenderer;
use Magento\Framework\Pricing\Render\Amount;
use Magento\Theme\Block\Html\Title;
use Magento\Catalog\Block\Product\View\Description;

class Replacer
{
    /**
     * @param ReviewRenderer|Amount|Title|Description $subject
     * @param string $result
     *
     * @return string
     */
    public function afterToHtml(
        $subject,
        $result
    ) {
        $result = preg_replace('|itemprop=".*"|U', '', $result);
        $result = preg_replace('|itemtype=".*"|U', '', $result);
        $result = str_replace('itemscope', '', $result);

        return $result;
    }
}
