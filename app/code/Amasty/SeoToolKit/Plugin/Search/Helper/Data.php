<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Plugin\Search\Helper;

use Magento\Search\Helper\Data as NativeData;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\SeoToolKit\Helper\Config;

class Data
{
    /**
     * @var Config
     */
    private $helper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        Config $helper
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param NativeData $subject
     * @param \Closure $proceed
     * @param string $query
     * @return string
     */
    public function aroundGetResultUrl(
        NativeData $subject,
        \Closure $proceed,
        $query = null
    ) {
        $seoKey = $this->helper->getSeoKey();
        if ($this->helper->isSeoUrlsEnabled() && $seoKey && $query) {
            $url = rtrim($this->storeManager->getStore()->getBaseUrl(), '/') . '/' . $seoKey . '/' . $query;
        } else {
            $url = $proceed($query);
        }

        return $url;
    }
}
