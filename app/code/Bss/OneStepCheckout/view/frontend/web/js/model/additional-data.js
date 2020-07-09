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
    'jquery',
    'uiRegistry',
    'underscore'
], function ($, registry, _) {
    'use strict';

    return function (paymentData) {
        var additionalData = {};
        var shippingAddressComponent = registry.get('checkout.steps.shipping-step.shippingAddress');
        var deliveryDate = shippingAddressComponent.getChild('before-shipping-method-form').getChild('bss_osc_delivery_date');
        var deliveryComment = shippingAddressComponent.getChild('before-shipping-method-form').getChild('bss_osc_delivery_comment');
        var orderComment = registry.get('checkout.sidebar.bss_osc_order_comment');
        var subscribe = registry.get('checkout.sidebar.subscribe');

        if (!_.isUndefined(deliveryDate) && deliveryDate.value()) {
            additionalData['delivery_date'] = deliveryDate.value();
        }
        if (!_.isUndefined(deliveryComment) && deliveryComment.value()) {
            additionalData['delivery_comment'] = deliveryComment.value();
        }
        if (!_.isUndefined(orderComment) && orderComment.value()) {
            additionalData['order_comment'] = orderComment.value();
        }
        if (!_.isUndefined(subscribe)) {
            additionalData['subscribe'] = subscribe.value();
        }
        if (!additionalData) {
            return;
        }
        if (paymentData['extension_attributes'] === undefined) {
            paymentData['extension_attributes'] = {};
        }

        paymentData['extension_attributes']['bss_osc'] = additionalData;
    };
});
