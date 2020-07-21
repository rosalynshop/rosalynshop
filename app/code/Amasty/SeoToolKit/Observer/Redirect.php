<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Observer;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class Redirect implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    private $response;

    /**
     * @var \Amasty\SeoToolKit\Helper\Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Framework\App\State $appState,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Amasty\SeoToolKit\Helper\Config $config,
        \Magento\Framework\App\ResponseInterface $response,
        StoreManagerInterface $storeManager
    ) {
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
        $this->response = $response;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->appState->getAreaCode() == FrontNameResolver::AREA_CODE) {
            return;
        }

        /** @var Request $request */
        $request = $observer->getRequest();
        if ($request->getMethod() != 'GET' || !$this->config->getModuleConfig('general/home_redirect')) {
            return;
        }

        $baseUrl = $this->urlBuilder->getBaseUrl();
        $baseUrl = str_replace('index.php/', '', $baseUrl);
        if (!$baseUrl) {
            return;
        }

        $requestPath = $request->getRequestUri();
        $params = preg_split('/^.+?\?/', $request->getRequestUri());
        $baseUrl .= isset($params[1]) ? '?' . $params[1] : '';

        $redirectUrls = [
            '',
            '/cms',
            '/cms/',
            '/cms/index',
            '/cms/index/',
            '/index.php',
            '/index.php/',
            '/home',
            '/home/',
        ];

        if ($this->storeManager->getStore()->isUseStoreInUrl()) {
            $requestPath = preg_replace("@^/{$this->storeManager->getStore()->getCode()}@", '', $requestPath, 1);
        }

        if ($requestPath !== null && in_array($requestPath, $redirectUrls)) {
            $this->redirect($observer->getData('controller_action'), $baseUrl);
        }
    }

    /**
     * @param Action $action
     * @param string $redirectUrl
     */
    protected function redirect(Action $action, string $redirectUrl)
    {
        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $action->getResponse();
        $response->setRedirect($redirectUrl, 301);
        $action->getActionFlag()->set('', ActionInterface::FLAG_NO_DISPATCH, true);
    }
}
