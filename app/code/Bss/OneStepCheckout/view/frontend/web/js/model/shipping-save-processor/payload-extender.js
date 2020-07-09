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
    'jquery'
], function ($) {
    'use strict';

    return function (payload) {
        payload.addressInformation['extension_attributes'] = {};
        if ($('#shipping #shipping_arrival_date').length || $('#opc-shipping_method #shipping_arrival_date').length) {
            payload.addressInformation['extension_attributes']['shipping_arrival_date'] = $('#shipping_arrival_date').val();
        }
        if ($('#shipping #shipping_arrival_comments').length || $('#opc-shipping_method #shipping_arrival_comments').length) {
            payload.addressInformation['extension_attributes']['shipping_arrival_comments'] = $('#shipping_arrival_comments').val();
        }
        if ($('#shipping #delivery_time_slot').length || $('#opc-shipping_method #delivery_time_slot').length) {
            payload.addressInformation['extension_attributes']['delivery_time_slot'] = $('#delivery_time_slot').val();
        }
        return payload;
    };
});
