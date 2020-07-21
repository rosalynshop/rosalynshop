<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Plugin;

use \Magento\Theme\Block\Html\Pager as NativePager;
use Magento\Framework\Url\Helper\Data as UrlHelper;

class Pager
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    public function __construct(
        UrlHelper $urlHelper,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->urlHelper = $urlHelper;
        $this->escaper = $escaper;
    }

    /**
     * Remove ?p=1 param from url
     * @param NativePager $subject
     * @param $result
     * @return string
     */
    public function afterGetPageUrl(
        NativePager $subject,
        $result
    ) {
        $this->removeFirstPageParam($result);

        return $result;
    }

    /**
     * @param string $url
     */
    private function removeFirstPageParam(&$url)
    {
        /* check if url not ?p=10*/
        if (strpos($url, 'p=1&') !== false
            || strlen($url) - stripos($url, 'p=1')  === strlen('p=1')//in the end of line
        ) {
            $url = htmlspecialchars_decode($url);
            $url = $this->urlHelper->removeRequestParam($url, 'p');
            $url = $this->escaper->escapeHtml($url);
        }
    }
}
