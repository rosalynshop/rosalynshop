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



namespace Mirasvit\CacheWarmer\Plugin;

use Magento\Framework\App\Helper\Context as ContextHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Mirasvit\CacheWarmer\Api\Data\UserAgentInterface;
use Mirasvit\CacheWarmer\Api\Service\PageServiceInterface;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Service\Config\ExtendedConfig;

class CollectPlugin
{
    /**
     * @var PageServiceInterface
     */
    private $pageService;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        PageServiceInterface $pageService,
        Registry $registry,
        ExtendedConfig $extendedConfig,
        Config $config,
        ContextHelper $contextHelper
    ) {
        $this->pageService    = $pageService;
        $this->request        = $contextHelper->getRequest();
        $this->registry       = $registry;
        $this->extendedConfig = $extendedConfig;
        $this->config         = $config;
        $this->contextHelper  = $contextHelper;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRenderResult(
        $subject,
        \Closure $proceed,
        ResponseInterface $response
    ) {
        $userAgent = $this->contextHelper->getHttpHeader()->getHttpUserAgent();
        //ignore collect if "Warm mobile pages separately" enabled
        if ($this->extendedConfig->isWarmMobilePagesEnabled() && $userAgent == UserAgentInterface::MOBILE_USER_AGENT) {
            return $proceed($response);
        }
        //ignore collect if user-agent ignoring
        if ($this->config->isIgnoredUserAgent($userAgent)) {
            return $proceed($response);
        }

        $this->pageService->collect($this->request, $response);

        return $proceed($response);
    }
}
