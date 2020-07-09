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

/**
 * @api
 */
define([
    'Bss_OneStepCheckout/js/model/shipping-save-processor/default'
], function (defaultProcessor) {
    'use strict';

    var processors = [];

    processors['default'] =  defaultProcessor;

    return {
        /**
         * @param {String} type
         * @param {*} processor
         */
        registerProcessor: function (type, processor) {
            processors[type] = processor;
        },

        /**
         * @param {String} type
         * @return {Array}
         */
        saveShippingInformation: function (type) {
            var rates = [];

            if (processors[type]) {
                rates = processors[type].saveShippingInformation();
            } else {
                rates = processors['default'].saveShippingInformation();
            }

            return rates;
        }
    };
});
