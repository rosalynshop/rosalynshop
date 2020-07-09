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
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Bss_OneStepCheckout/js/model/payment-service-default',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/action/select-billing-address',
    'Bss_OneStepCheckout/js/model/shipping-save-processor/payload-extender',  // fix 2.2.0
    'Magento_Customer/js/model/customer' // fix 2.1.x
], function (
    ko,
    _,
    quote,
    resourceUrlManager,
    storage,
    paymentService,
    methodConverter,
    errorProcessor,
    selectBillingAddressAction,
    payloadExtender,
    customer
) {
    'use strict';

    return {
        /**
         * @return {jQuery.Deferred}
         */
        saveShippingInformation: function () {
            var payload;

            if (!quote.billingAddress()) {
                selectBillingAddressAction(quote.shippingAddress());
            }

            var billingAddress = quote.billingAddress();

            if (!customer.isLoggedIn()) {
                if (billingAddress) {
                    if (!_.isUndefined(billingAddress.street)) {
                        if (billingAddress.street.length == 0) {
                            delete billingAddress.street;
                        }
                    } else {
                        delete billingAddress.street;
                    }
                }
            }

            payload = {
                addressInformation: {
                    'shipping_address': quote.shippingAddress(),
                    'billing_address': quote.billingAddress(),
                    'shipping_method_code': quote.shippingMethod()['method_code'],
                    'shipping_carrier_code': quote.shippingMethod()['carrier_code']
                }
            };

            payloadExtender(payload);

            return storage.post(
                resourceUrlManager.getUrlForSetShippingInformation(quote),
                JSON.stringify(payload)
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            );
        }
    };
});
