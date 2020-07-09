<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Plugin\Execute\Checkout\Index;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Checkout\Controller\Index\Index as CheckoutIndex;
use Bss\OneStepCheckout\Helper\Config;
use Magento\Framework\UrlInterface;

/**
 * Class Index
 *
 * @package Bss\OneStepCheckout\Plugin\Execute\Checkout\Index
 */
class Index
{
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
     * @param RedirectFactory $resultRedirectFactory
     * @param Config $configHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        RedirectFactory $resultRedirectFactory,
        Config $configHelper,
        UrlInterface $urlBuilder
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->configHelper = $configHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param CheckoutIndex $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundExecute(
        CheckoutIndex $subject,
        $proceed
    ) {
        if ($this->configHelper->isEnabled()) {
            $path = Config::OSC_CONTROLLER_NAME;
            $router = $this->configHelper->getGeneral('router_name');
            if ($router) {
                $router = preg_replace('/\s+/', '', $router);
                $router = preg_replace('/\/+/', '', $router);
                $path = trim($router, '/');
            }
            $url = trim($this->urlBuilder->getUrl($path), '/');
            return $this->resultRedirectFactory->create()->setUrl($url);
        } else {
            return $proceed();
        }
    }
}
