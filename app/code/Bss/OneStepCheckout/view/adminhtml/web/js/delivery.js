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

    if ($('#bss-osc-delivery-date').length) {
        var date = $('#bss-osc-delivery-date').detach();
        $('.order-shipping-method').append(date);
    }
    if ($('#bss-osc-delivery-comment').length) {
        var comment = $('#bss-osc-delivery-comment').detach();
        $('.order-shipping-method').append(comment);
    }
});
