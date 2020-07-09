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

namespace Bss\OneStepCheckout\Plugin\Helper\Checkout;

use Bss\OneStepCheckout\Helper\Config;

/**
 * Class Data
 *
 * @package Bss\OneStepCheckout\Plugin\Helper\Checkout
 */
class Data
{
    /**
     * OSC config helper.
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
     * @param \Magento\Checkout\Helper\Data $subject
     * @param callable $proceed
     * @return bool
     */
    public function afterIsDisplayBillingOnPaymentMethodAvailable(
        \Magento\Checkout\Helper\Data $subject,
        $result
    ) {
        if ($this->configHelper->isEnabled()) {
            $result = false;
        }
        return $result;
    }
}
