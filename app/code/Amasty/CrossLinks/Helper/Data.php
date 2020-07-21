<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Data
 * @package Amasty\CrossLinks\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TYPE_PRODUCT  = 'product';
    const TYPE_CATEGORY = 'category';
    const TYPE_CMS      = 'cms_page';

    /**
     * @param string $entity
     * @return array
     */
    public function getEntityReplacementAttributeCodes($entity = self::TYPE_PRODUCT)
    {
        if (empty($entity)) {
            return [];
        }

        $attributeCodes = $this->scopeConfig->getValue(
            'amasty_cross_links/general/' . $entity . '_replacement_attributes',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        return explode(',', $attributeCodes);
    }

    /**
     * @param string $entity
     * @return int
     */
    public function getEntityReplacementLimit($entity = self::TYPE_PRODUCT)
    {
        return (int)$this->scopeConfig->getValue(
            'amasty_cross_links/general/' . $entity . '_replacement_limit',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->scopeConfig->isSetFlag(
            'amasty_cross_links/general/enabled',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * @return bool
     */
    public function isActiveForFaq()
    {
        return $this->scopeConfig->isSetFlag(
            'amasty_cross_links/faq/enabled',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * @return bool
     */
    public function getFaqReplacementLimit()
    {
        return (int)$this->scopeConfig->getValue(
            'amasty_cross_links/faq/replacement_limit',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) ?: 1;
    }

    /**
     * @return string
     */
    public function getAdvancedRegexpr()
    {
        return $this->scopeConfig->getValue(
            'amasty_cross_links/advanced/regexpr',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }
}
