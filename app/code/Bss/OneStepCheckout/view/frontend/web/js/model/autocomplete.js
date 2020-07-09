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
    'uiComponent',
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/lib/view/utils/async'
], function (ko, _, Component, $,registry) {
    'use strict';

    var componentForm = {
        street_number: ['street_number', 'long_name'],
        route: ['street', 'long_name'],
        locality: ['city', 'long_name'],
        administrative_area_level_1: ['region', 'region_id'],
        administrative_area_level_2: ['region_id_level_2', 'long_name'],
        country: ['country_id', 'short_name'],
        postal_code: ['postcode', 'long_name']
    };

    var componentFields = [
        'country_id',
        'postcode',
        'region_id',
        'region',
        'city',
        'street'
    ];

    return Component.extend({

        /** @inheritdoc */
        initialize: function () {
            this._super();
            var self = this,
                mapUrl;
            if (!_.isUndefined(window.checkoutConfig.bssOsc.googleApi)) {
                mapUrl = 'https://maps.googleapis.com/maps/api/js?key='+window.checkoutConfig.bssOsc.googleApi+'&libraries=places';
                $.getScript(mapUrl);
            }

            $.async('[name="street[0]"]',function (element) {
                var id = $(element).attr('id');

                self.initAutocomplete(id);
                $(element).attr('placeholder', '');
            });
        },

        /**
         * @param {Object} autocomplete
         */
        geolocate: function (autocomplete) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        },

        /**
         * @param {String} id
         */
        initAutocomplete: function (id) {
            var self = this,
                options = {types: ['address']};

            if (!_.isUndefined(window.checkoutConfig.bssOsc.specificcountry)) {
                var countries = window.checkoutConfig.bssOsc.specificcountry;
                options.types = ['(cities)'];
                options.componentRestrictions = {country: countries};
            }
            var autocomplete = new google.maps.places.Autocomplete(
                (document.getElementById(id)),
                options
            );

            autocomplete.addListener('place_changed', function () {
                self.fillInAddress(id, autocomplete);
            });
            this.geolocate(autocomplete);
        },

        /**
         * @param {String} id
         * @param {Object} autocomplete
         */
        fillInAddress: function (id, autocomplete) {
            var place = autocomplete.getPlace(),
                result = {},
                street = '',
                self = this,
                component,
                addressComponents = place.address_components,
                billing = 'checkout.steps.billing-step.payment.payments-list.billing-address-form-shared.form-fields',
                shipping = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset';
            if (registry.get(shipping).getChild('street').getChild(0).uid === id) {
                component = shipping;
            } else {
                component = billing;
            }
            $.each(addressComponents, function (index, address) {
                var addressType = address.types[0];
                if (componentForm[addressType]) {
                    if (addressType === 'administrative_area_level_1') {
                        result[componentForm[addressType][0]] = address['long_name'];
                        result[componentForm[addressType][1]] = address['long_name'];
                    } else if (addressType === 'route') {
                        if (street) {
                            street += ' ';
                        }
                        street += address[componentForm[addressType][1]];
                        result['street'] = street;
                    } else if (addressType === 'street_number') {
                        if (street) {
                            street += ' ';
                        }
                        street += address[componentForm[addressType][1]];
                        result['street'] = street;
                    } else {
                        result[componentForm[addressType][0]] = address[componentForm[addressType][1]];
                    }
                }
            });
            if (!street) {
                result['street'] = $('#' + id).val().split(',')[0];
            }
            if (!result['city'] && result['region']) {
                result['city'] = result['region'];
            }
            registry.get(component, function (formComponent) {
                $.each(componentFields, function (index, field) {
                    var value = result[field];
                    if (field === 'region') {
                        field = 'region_id_input';
                    }
                    var element = formComponent.getChild(field);

                    if (field === 'street') {
                        element = formComponent.getChild(field).getChild(0);
                    }
                    if (field !== 'country_id') {
                        element.value('');
                    }
                    if (formComponent.hasChild(field)) {
                        if (!formComponent.getChild(field).visible()) {
                            return;
                        }
                        if (field === 'region_id') {
                            var regionOptions = element.indexedOptions;
                            value = self.filterRegion(regionOptions, result, 'region_id');
                            if (_.isUndefined(value)) {
                                value = self.filterRegion(regionOptions, result, 'region_id_level_2');
                            }
                        }
                        element.value(value);
                    }
                });
            });
        },

        /**
         * @param {Object} regionOptions
         * @param {Array} result
         * @param {String} index
         */
        filterRegion: function (regionOptions, result, index) {
            var select;
            _.filter(regionOptions, function (optionData) {
                if (optionData.title === result[index]) {
                    select = optionData.value;
                }
            });
            return select;
        }
    });
});
