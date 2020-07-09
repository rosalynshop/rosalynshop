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

define([
    'ko',
    'jquery',
    'uiComponent',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/quote',
    'Bss_OneStepCheckout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/select-billing-address',
    'Bss_OneStepCheckout/js/model/place-order-btn-service',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/shipping-service',
    'underscore',
    'Magento_Ui/js/modal/alert'
], function (
    ko,
    $,
    Component,
    registry,
    $t,
    quote,
    setShippingInformationAction,
    fullScreenLoader,
    selectBillingAddress,
    placeOrderBtnService,
    additionalValidators,
    shippingService,
    _,
    alert
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Bss_OneStepCheckout/place-order-btn'
        },

        placeOrderLabel: ko.observable($t('Place Order')),

        isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null && quote.paymentMethod() != null),

        /** @inheritdoc */
        initialize: function () {
            this._super();
            var self = this;
            quote.billingAddress.subscribe(function (address) {
                if (quote.isVirtual()) {
                    setTimeout(function () {
                        self.isPlaceOrderActionAllowed(address !== null && quote.paymentMethod() != null);
                    }, 500);
                } else {
                    self.isPlaceOrderActionAllowed(address !== null && quote.paymentMethod() != null && quote.shippingMethod() != null);
                }
            }, this);
            quote.paymentMethod.subscribe(function (newMethod) {
                if (quote.isVirtual()) {
                    self.isPlaceOrderActionAllowed(newMethod !== null && quote.billingAddress() != null);
                } else {
                    self.isPlaceOrderActionAllowed(newMethod !== null && quote.billingAddress() != null && quote.shippingMethod() != null);
                }
            }, this);
            if (!quote.isVirtual()) {
                quote.shippingMethod.subscribe(function (method) {
                    var availableRate,
                        shippingRates = shippingService.getShippingRates();
                    if (method) {
                        availableRate = _.find(shippingRates(), function (rate) {
                            return rate['carrier_code'] + '_' + rate['method_code'] === method['carrier_code'] + '_' + method['method_code'];
                        });
                    }
                    self.isPlaceOrderActionAllowed(availableRate && quote.paymentMethod() != null && quote.billingAddress() != null);
                }, this);
            }
        },

        placeOrder: function (data, event) {
            var self = this;
            var shippingAddressComponent = registry.get('checkout.steps.shipping-step.shippingAddress');
            var billingAddressComponent = registry.get('checkout.steps.billing-step.payment.payments-list.billing-address-form-shared');

            if (event) {
                event.preventDefault();
            }

            if (additionalValidators.validate()) {
                if (quote.isVirtual()) {
                    $('input#' + self.getCode())
                        .closest('.payment-method').find('.payment-method-content .actions-toolbar:not([style*="display: none"]) button.action.checkout')
                        .trigger('click');
                } else {
                    placeOrderBtnService.isEnable(true);
                    if (shippingAddressComponent.validateShippingInformation()) {
                        fullScreenLoader.startLoader();
                        if (billingAddressComponent.isAddressSameAsShipping()) {
                            selectBillingAddress(quote.shippingAddress());
                        }
                        setShippingInformationAction().done(
                            function () {
                                setTimeout(function () {
                                    $('input#' + self.getCode())
                                        .closest('.payment-method').find('.payment-method-content .actions-toolbar:not([style*="display: none"]) button.action.checkout')
                                        .trigger('click');
                                }, 500);
                            }
                        ).always(
                            function () {
                                setTimeout(function () {
                                    fullScreenLoader.stopLoader();
                                }, 5000);
                            }
                        );
                    } else {
                        placeOrderBtnService.isEnable(false);
                    }
                }
            } else {
                alert({
                    title: $t('Note'),
                    content: $t('Please Enter All Required Field.')
                });
            }
            return false;
        },

        getCode: function () {
            return quote.paymentMethod().method;
        }
    });
});
