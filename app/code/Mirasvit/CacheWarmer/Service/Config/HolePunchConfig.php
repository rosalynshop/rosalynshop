<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Service\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class HolePunchConfig
{
    const FROM_CACHE           = 'm_from_cache';
    const FIND_DATA            = 'm_find_data_by_pattern_data';
    const CMS_BLOCK_EXCLUDE    = 'm_cms_block_exclude';
    const WIDGET_BLOCK_EXCLUDE = 'm_widget_block_exclude';

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int|null $store
     * @return array
     */
    public function getTemplates($store = null)
    {
        $conf = $this->scopeConfig->getValue(
            'cache_warmer/hole_punch/hole_punch_templates',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
        if ($decode = json_decode($conf)) {
            $conf = $decode;
        } else {
            if ($conf != '[]') {
                $conf = unserialize($conf);
            } else {
                $conf = [];
            }
        }
        if (is_object($conf)) {
            $conf = (array)$conf;
            foreach ($conf as $key => $value) {
                if (is_object($value)) {
                    $conf[$key] = (array)$value;
                }
            }
        }

        if (is_array($conf)) {
            foreach ($conf as $confKey => $confData) {
                if (!$confData['template'] || !$confData['block']) {
                    unset($conf[$confKey]);
                }
            }
        }

        return $conf;
    }
}