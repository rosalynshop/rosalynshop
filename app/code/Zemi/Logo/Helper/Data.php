<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\Logo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Zemi\FacebookChat\Helper
 */
class Data extends AbstractHelper
{
    const XML_ZEMI_LOGO_ENABLE = 'zemilogo/general/enable';
    const XML_ZEMI_LABEL_1 = 'zemilogo/general/label_1';
    const XML_ZEMI_LABEL_2 = 'zemilogo/general/label_2';
    const XML_ZEMI_COLOR_1 = 'zemilogo/general/color_1_option';
    const XML_ZEMI_COLOR_2 = 'zemilogo/general/color_2_option';
    const XML_ZEMI_SLOGAN = 'zemilogo/general/slogan';
    const XML_ZEMI_COLOR_SLOGAN = 'zemilogo/general/color_slogan';

    const XML_ZEMI_LOGO_SVG_ENABLE = 'zemilogo/logo_svg/enable';
    const XML_ZEMI_LOGO_SVG_HTML = 'zemilogo/logo_svg/sgv_html';

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_LOGO_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    /**
     * @param null $storeId
     * @return mixed
     */
    public function getLabel1Config($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_LABEL_1,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getLabel2Config($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_LABEL_2,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getLogoColorLabel1($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_COLOR_1,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getLogoColorSlogan($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_COLOR_SLOGAN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getLogoColorLabel2($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_COLOR_2,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getLogoSlogan($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_SLOGAN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function svgEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_LOGO_SVG_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function svgHtml($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_LOGO_SVG_HTML,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
