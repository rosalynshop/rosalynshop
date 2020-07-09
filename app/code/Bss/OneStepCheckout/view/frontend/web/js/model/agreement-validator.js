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
    'mage/validation'
], function ($) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {},
        agreementsInputPath = '.bss-onestepcheckout #opc-sidebar div.checkout-agreements input';

    return {
        /**
         * Validate checkout agreements
         *
         * @returns {Boolean}
         */
        validate: function () {
            var isValid = true;

            if (!agreementsConfig.isEnabled || $(agreementsInputPath).length === 0) {
                return true;
            }

            $(agreementsInputPath).each(function (index, agreementsInput) {
                if (!$('#bss-osc-form-checkout-agreements').validate({
                    errorClass: 'mage-error',
                    errorElement: 'div',
                    meta: 'validate',
                    errorPlacement: function (error, element) {
                        var errorPlacement = element;
                        if (element.is(':checkbox') || element.is(':radio')) {
                            errorPlacement = element.siblings('label').last();
                        }
                        errorPlacement.after(error);
                    }
                }).element(agreementsInput)) {
                    isValid = false;
                }
            });

            return isValid;
        }
    };
});
