<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Plugin\Checkout\Controller\Index;

use Aheadworks\OneStepCheckout\Helper\Config;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Checkout\Controller\Index\Index as CheckoutIndex;
use Magento\Framework\Module\Manager;
use Magento\Framework\UrlInterface;

/**
 * Class Index
 * @package Aheadworks\OneStepCheckout\Plugin\Checkout\Controller\Index
 */
class Index
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Index constructor.
     * @param ResultFactory $resultFactory
     * @param Manager $moduleManager
     * @param RedirectFactory $resultRedirectFactory
     * @param Config $configHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ResultFactory $resultFactory,
        Manager $moduleManager,
        RedirectFactory $resultRedirectFactory,
        Config $configHelper,
        UrlInterface $urlBuilder
    ) {
        $this->resultFactory = $resultFactory;
        $this->moduleManager = $moduleManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->configHelper = $configHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Perform forward to one step checkout action if needed
     *
     * @param CheckoutIndex $subject
     * @param \Closure $proceed
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(CheckoutIndex $subject, \Closure $proceed)
    {
        if ($this->isNeedToPerformForwardToOneStepCheckout()) {
            $router = $this->configHelper->getGeneral('router_name');
            if ($router) {
                $router = preg_replace('/\s+/', '', $router);
                $router = preg_replace('/\/+/', '', $router);
                $path = trim($router, '/');
                $url = trim($this->urlBuilder->getUrl($path), '/');
                return $this->resultRedirectFactory->create()->setUrl($url);
            } else {
                /** @var Forward $resultForward */
                $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
                $result = $resultForward
                    ->setModule('onestepcheckout')
                    ->setController('index')
                    ->forward('index');
            }
        } else {
            $result = $proceed();
        }
        return $result;
    }

    /**
     * Check if need to perform forward to one step checkout
     *
     * @return bool
     */
    private function isNeedToPerformForwardToOneStepCheckout()
    {
        return $this->moduleManager->isOutputEnabled('Aheadworks_OneStepCheckout');
    }
}
