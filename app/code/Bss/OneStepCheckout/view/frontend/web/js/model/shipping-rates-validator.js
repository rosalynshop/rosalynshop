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
    'jquery',
    'ko',
    'underscore',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/postcode-validator',
    'Bss_OneStepCheckout/js/model/default-validator',   // fix 2.1
    'mage/translate',
    'uiRegistry',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rates-validator'
], function (
    $,
    ko,
    _,
    shippingRatesValidationRules,
    addressConverter,
    selectShippingAddress,
    postcodeValidator,
    defaultValidator,
    $t,
    uiRegistry,
    formPopUpState,
    quote,
    shippingRatesValidator
) {
    'use strict';

    var validators = [],
        postcodeElement = null,
        postcodeElementName = 'postcode';

    validators.push(defaultValidator);

    shippingRatesValidator.doElementBinding = function (element, force, delay) {
        var observableFields = shippingRatesValidationRules.getObservableFields();
        if (_.isUndefined(delay)) {
            if (element.index === 'country_id' || element.index === 'region_id') {
                delay = 0;
            } else {
                delay = 700;
            }
        }
        if (element && (observableFields.indexOf(element.index) !== -1 || force)) {
            if (element.index !== postcodeElementName) {
                this.bindHandler(element, delay);
            }
        }

        if (element.index === postcodeElementName) {
            this.bindHandler(element, delay);
            postcodeElement = element;
        }
    }

    return shippingRatesValidator;
});
