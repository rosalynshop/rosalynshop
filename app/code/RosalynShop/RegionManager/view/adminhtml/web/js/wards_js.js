/*
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

define([
    'jquery',
    'mage/url',
    'jquery/ui',
], function ($, urlBuilder) {
    "use strict";

    $.widget('rosalynshop.wardsjs', {
        options: {
            wardsAjax: {
                wardsAjaxUrl: null,
                citiesJsAjaxUrl: null
            },
            method: 'post',
            stateSelector: 'select[name="states_name"]',
            citiesName : 'select[name="cities_name"]',
        },
        _create: function () {
            this._bind();
        },

        _bind: function () {
            this._load();
        },

        _load: function(){
            var self = this;
            setTimeout(function () {
                self._loadStates();
                self._loadCities();
            },1000);
        },

        _loadStates: function () {
            var self = this;
            $(self.options.stateSelector).each(function () {
                $.ajax({
                    url: self.options.wardsAjax.wardsAjaxUrl,
                    type: self.options.method,
                    data: {
                        form_key: window.FORM_KEY,
                        'selected_state': $(this).val()
                    },
                    dataType: 'json',
                    showLoader: true,
                    success: function (data) {
                        var $citiesSelect = self.options.citiesName;
                        if (data.request == 'OK') {
                            $($citiesSelect).each(function () {
                                if ($(this).val() == '')
                                    var $select = $('<option data-title="-- Vui lòng chọn --" value="">-- Vui lòng chọn --</option>');
                                    $($citiesSelect).empty().append($select);
                            });
                            $.each(data.result, function () {
                                $($citiesSelect).append($('<option data-title="' + this.cities_name + '" value="' + this.cities_name + '">' + this.cities_name + '</option>'));
                            });
                        } else {
                            $($citiesSelect).empty().append($('<option data-title="' + data.result + '" value="' + data.result + '">' + data.result + '</option>'));
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });
        },

        _loadCities: function () {
            var self = this;
            $(self.options.stateSelector).change(function () {
                var loading = $('<option data-title="Đang tải"'+' value="">Đang tải...</option>');
                if ($(this).val() != "") {
                    $(self.options.citiesName).empty().append(loading);
                }
                $.ajax({
                    url: self.options.wardsAjax.wardsAjaxUrl,
                    type: self.options.method,
                    data: {
                        form_key: window.FORM_KEY,
                        'selected_state': $(this).val()
                    },
                    dataType: 'json',
                    showLoader: true,
                    success: function (data) {
                        var $citiesSelect = self.options.citiesName;
                        if (data.request == 'OK') {
                            var $select = $('<option data-title="-- Vui lòng chọn --" value="">-- Vui lòng chọn --</option>');
                            $($citiesSelect).empty().append($select);
                            $.each(data.result, function () {
                                $($citiesSelect).append($('<option data-title="' + this.cities_name + '" value="' + this.cities_name + '">' + this.cities_name + '</option>'));
                            });
                        } else {
                            $($citiesSelect).empty().append($('<option data-title="' + data.result + '" value="' + data.result + '">' + data.result + '</option>'));
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });
        }
    });

    return $.rosalynshop.wardsjs;
});
