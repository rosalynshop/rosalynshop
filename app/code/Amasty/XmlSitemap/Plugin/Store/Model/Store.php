<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Plugin\Store\Model;

use Magento\Framework\Registry;

class Store
{
    /**
     * @var Registry
     */
    private $registry;

    function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function afterIsCurrentlySecure(
        \Magento\Store\Model\Store $subject,
        $isSecure
    ) {
        if ($this->registry->registry(\Amasty\XmlSitemap\Model\Sitemap::SITEMAP_GENERATION) &&
            $subject->isUrlSecure()
        ) {
            $isSecure = true;
        }

        return $isSecure;
    }
}
