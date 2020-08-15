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



namespace Mirasvit\CacheWarmer\Plugin\Debug;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\Element;
use Mirasvit\CacheWarmer\Model\Config;

class LayoutPlugin
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param Layout $layout
     * @param string $result
     * @return string
     */
    public function afterGetOutput(Layout $layout, $result)
    {
        $nonCacheableBlocks = [];

        $paths = $layout->getUpdate()->asSimplexml()->xpath('//' . Element::TYPE_BLOCK . '[@cacheable="false"]');
        if (count($paths)) {
            foreach ($paths as $path) {
                $class = $path['class'];
                $name  = $path['name'];

                $nonCacheableBlocks[(string)$class] = (string)$name;
            }
        }

        if ($this->config->isDebugToolbarEnabled() && trim($result) != "") {
            $result .= "<div class='mst-cache-warmer__ncb' data-ncb='" .
                base64_encode(\Zend_Json::encode($nonCacheableBlocks)) . "'></div>";
        }

        return $result;
    }
}
