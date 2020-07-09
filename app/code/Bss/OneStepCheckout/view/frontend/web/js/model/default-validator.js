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
    'mageUtils',
    './default-validation-rules',
    'mage/translate'
], function ($, utils, validationRules, $t) {
    'use strict';

    return {
        validationErrors: [],

        /**
         * @param {Object} address
         * @return {Boolean}
         */
        validate: function (address) {
            var self = this;

            this.validationErrors = [];
            $.each(validationRules.getRules(), function (field, rule) {
                var message;

                if (rule.required && utils.isEmpty(address[field])) {
                    message = $t('Field ') + field + $t(' is required.');

                    self.validationErrors.push(message);
                }
            });

            return !this.validationErrors.length;
        }
    };
});
