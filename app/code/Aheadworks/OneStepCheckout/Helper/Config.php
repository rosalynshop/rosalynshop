<?php

namespace Aheadworks\OneStepCheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\OneStepCheckout\Helper
 */
class Config extends AbstractHelper
{
    const GENERAL_GROUP = 'aw_osc/general/';

    /**
     * @param string $field
     * @param null|int $storeId
     * @return mixed
     */
    public function getGeneral($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::GENERAL_GROUP . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
