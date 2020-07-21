<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Helper;

use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MODULE_NAME = 'amseotoolkit/';
    const XML_PATH_IS_SEO_URL_ENABLED = 'general/enable_seo_url';
    const XML_PATH_SEO_KEY = 'general/seo_key';

    public function getModuleConfig($path)
    {
        return $this->scopeConfig->getValue(
            self::MODULE_NAME . $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isPrevNextLinkEnabled()
    {
        return $this->getModuleConfig('pager/prev_next');
    }

    public function isAddPageToMetaTitleEnabled()
    {
        return $this->getModuleConfig('pager/meta_title');
    }

    public function isAddPageToMetaDescEnabled()
    {
        return $this->getModuleConfig('pager/meta_description');
    }

    public function isNoIndexNoFollowEnabled()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isSeoUrlsEnabled()
    {
        return (bool)$this->getModuleConfig(self::XML_PATH_IS_SEO_URL_ENABLED);
    }

    /**
     * @return string
     */
    public function getSeoKey()
    {
        return (string)urlencode(trim($this->getModuleConfig(self::XML_PATH_SEO_KEY)));
    }
}
