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

namespace Bss\OneStepCheckout\Plugin\Block\Checkout\Checkout;

use Bss\OneStepCheckout\Helper\Config;

/**
 * Class LayoutProcessor
 *
 * @package Bss\OneStepCheckout\Plugin\Block\Checkout\Checkout
 */
class LayoutProcessor
{
    /**
     * One step checkout helper
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
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        if (!$this->configHelper->isEnabled()) {
            return $jsLayout;
        }
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            $component = $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['afterMethods']['children']
                ['billing-address-form'];
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children']['billing-address-form']
            );
            $component['component'] = 'Bss_OneStepCheckout/js/view/billing-address';
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children']
            ['billing-address-form-shared'] = $component;
        }

        if (!$this->configHelper->isDisplayField('enable_delivery_date')) {
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['before-shipping-method-form']['children']
                ['bss_osc_delivery_date']
            );
        }

        if (!$this->configHelper->isDisplayField('enable_delivery_comment')) {
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['before-shipping-method-form']['children']
                ['bss_osc_delivery_comment']
            );
        }

        if (!$this->configHelper->isDisplayField('enable_order_comment')) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['bss_osc_order_comment']
            );
        }

        if (!$this->configHelper->isDisplayField('enable_subscribe_newsletter')) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['subscribe']
            );
        } else {
            $checked = (bool) $this->configHelper->getGeneral('newsletter_default');
            $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['subscribe']['config']['checked'] = $checked;
        }

        if (!$this->configHelper->isDisplayField('enable_gift_message') ||
            !$this->configHelper->isMessagesAllowed()) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['giftmessage']
            );
        }

        if ($this->configHelper->isDisplayField('enable_discount_code')) {
            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount'] =
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children']['discount'];

            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount']
            ['displayArea'] = 'summary';
            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount']
            ['template'] = 'Bss_OneStepCheckout/payment/discount';

            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount']
            ['sortOrder'] = 230;
        }

        if (!$this->configHelper->isAutoComplete()) {
            unset($jsLayout['components']['checkout']['children']['autocomplete']);
        }

        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children']['discount']
        );

        unset(
            $jsLayout['components']['checkout']['children']['sidebar']['children']['shipping-information']
        );

        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children']['before-place-order']
            ['children']['agreements']
        );

        unset($jsLayout['components']['checkout']['children']['progressBar']);
        return $jsLayout;
    }
}
