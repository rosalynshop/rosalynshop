<?php
/**
 * @author   Rosalynshop <info@rosalynshop.com>
 * @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\SocialShare\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Zemi\SocialShare\Helper
 */
class Data extends AbstractHelper
{
    const XML_ZEMI_SHARE_ENABLE = 'zmsocialshare/general/enable';
    const XML_ZEMI_FACEBOOK_ENABLE = 'zmsocialshare/facebook/enable';
    const XML_ZEMI_FACEBOOK_SCRIPT = 'zmsocialshare/facebook/src_script';

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isModuleEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_SHARE_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isFacebookEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_FACEBOOK_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function srcScriptFacebook($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_FACEBOOK_SCRIPT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
