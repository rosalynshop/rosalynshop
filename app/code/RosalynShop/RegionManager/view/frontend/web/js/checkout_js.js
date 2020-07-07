/*
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

define([
    'jquery',
    'mage/url',
    'mage/translate',
    'jquery/ui'
], function ($, urlBuilder, $t) {
    "use strict";

    $.widget('rosalynshop.regionManager', {
        options: {
            processStart: null,
            processStop: null,
            statesSelect: '.state-drop-down select.select',
            citiesSelect: '.city-drop-down select.select',
            citiesAjaxUrl: 'regionmanager/ajax/getcities'
        },

        _create: function () {
            this._bind();
        },

        _bind: function () {
            this._load();
        },

        _load: function () {
            var self = this;
            var search = function () {
                self.hidenCountry();
                self.hiddenSamBillingAddress();
                var $statesSelect = $(self.options.statesSelect).text();
                if ($statesSelect.length > 1) {
                    self._loadStates();
                    clearInterval(intervalID);
                }
            };
            var intervalID = setInterval(search, 500);
        },

        hidenCountry: function () {
            var coutry = $("div[name='shippingAddress.country_id']");
            if (typeof coutry !== 'undefined') {
                coutry.hide();
            }
        },

        hiddenSamBillingAddress: function () {
            $('.onestep-billing-address').hide();
        },

        _loadStates: function () {
            var self = this;
            var citiesSelect = $(self.options.citiesSelect);

            $(self.options.statesSelect).change(function () {
                var statesSelect = $(this).children("option:selected").val();
                if (typeof statesSelect !== 'undefined' && statesSelect.length > 1) {
                    $(citiesSelect).empty().append($('<option data-title="load" value="">' + $t('Loading...') + '</option>'));
                    self.ajaxLoadingCity(statesSelect, citiesSelect);
                } else {
                    $(citiesSelect).empty().append($('<option data-title="load" value="">' + $t('-- Vui lòng chọn tỉnh thành trước --') + '</option>'));
                }
            });
        },

        ajaxLoadingCity: function(statesSelect, citiesSelect) {
            var self = this;
            $.ajax({
                url: urlBuilder.build(self.options.citiesAjaxUrl),
                type: 'post',
                data: {
                    'selected_state': statesSelect
                },
                dataType: 'json',
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function (data) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }
                    if (data.request == 'OK') {
                        $(citiesSelect).empty().append($('<option data-title="' + $t('-- Vui lòng chọn --') +'"' +' value="">' + $t('-- Vui lòng chọn --') +'</option>'));
                        $.each(data.result, function () {
                            $(citiesSelect).append($('<option data-title="' + this.cities_name + '" value="' + this.cities_name + '">' + this.cities_name + '</option>'));
                        });
                    } else {
                        $(citiesSelect).empty().append($('<option data-title="' + data.result + '" value="' + data.result + '">' + data.result + '</option>'));
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        },

        /**
         * @return {Boolean}
         */
        isLoaderEnabled: function () {
            return this.options.processStart && this.options.processStop;
        },

    });

    return $.rosalynshop.regionManager;
});
