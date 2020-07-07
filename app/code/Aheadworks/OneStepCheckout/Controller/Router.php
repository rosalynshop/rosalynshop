<?php

namespace Aheadworks\OneStepCheckout\Controller;

use Magento\Framework\App\RouterInterface;
use Aheadworks\OneStepCheckout\Helper\Config;
use Magento\Framework\App\RequestInterface;

/**
 * Class Router
 *
 * @package Aheadworks\OneStepCheckout\Controller
 */
class Router implements RouterInterface
{
    /**
     * One step checkout helper config
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @param Config $configHelper
     */
    public function __construct(
        Config $configHelper,
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->configHelper = $configHelper;
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|void
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $router = $this->configHelper->getGeneral('router_name');

        if ($router) {
            $router = preg_replace('/\s+/', '', $router);
            $router = preg_replace('/\/+/', '', $router);
            if ($identifier === $router) {
                $request->setModuleName('onestepcheckout')
                    ->setControllerName('index')
                    ->setActionName('index');
            }
        }
    }
}
