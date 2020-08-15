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



namespace Mirasvit\CacheWarmer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Service\Config\DebugConfig;

class Toolbar extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mirasvit_CacheWarmer::toolbar.phtml';

    /**
     * @var \Mirasvit\CacheWarmer\Model\Config
     */
    private $config;

    /**
     * @var DebugConfig
     */
    private $debugConfig;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        Config $config,
        DebugConfig $debugConfig,

        Context $context
    ) {
        $this->config      = $config;
        $this->debugConfig = $debugConfig;

        $this->context    = $context;
        $this->urlBuilder = $context->getUrlBuilder();

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if (!$this->debugConfig->isInfoBlockEnabled()) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return bool
     */
    public function isVarnishEnabled()
    {
        if ($this->config->getCacheType() != PageCacheConfig::BUILT_IN) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCacheableTestUrl()
    {
        return $this->urlBuilder->getUrl('cache_warmer/test/cacheable');
    }

    /**
     * @return string
     */
    public function getNonCacheableTestUrl()
    {
        return $this->urlBuilder->getUrl('cache_warmer/test/nonCacheable');
    }
}
