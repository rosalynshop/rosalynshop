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

namespace Bss\OneStepCheckout\App;

use Magento\Framework\App\RouterInterface;
use Bss\OneStepCheckout\Helper\Config;
use Magento\Framework\App\RequestInterface;

/**
 * Class Router
 *
 * @package Bss\OneStepCheckout\App
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
        Config $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * @param RequestInterface $request
     * @return void
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
