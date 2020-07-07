<?php
/**
 * @author   Rosalynshop <info@rosalynshop.com>
 * @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\FacebookChat\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package RosalynShop\FacebookChat\Helper
 */
class Data extends AbstractHelper
{
    const XML_FB_MESSENGER_ENABLE = 'messenger/general/enable';
    const XML_FB_PAGE_ID = 'messenger/general/page_id';
    const XML_FB_COLOR = 'messenger/general/color_option';

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_FB_MESSENGER_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    /**
     * @param null $storeId
     * @return mixed
     */
    public function getFBPageId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_FB_PAGE_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getFBColor($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_FB_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
