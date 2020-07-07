/*
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

define([
    'jquery',
    'mage/url',
    'mage/translate',
    'jquery/ui',
], function ($, urlBuilder, $t) {
    "use strict";

    $.widget('rosalynshop.edit', {
        options: {
            processStart: null,
            processStop: null,
            statesSelect: '.state-drop-down select.select',
            citiesSelect: '.city-drop-down select.select',
            citiesAjaxUrl: 'regionmanager/ajax/getcities'
        },

        _create: function () {
            this._bind();
            this._validateFormCustom();
        },

        _bind: function () {
            this._load();
        },

        _load: function(){
            var self = this,
                $statesSelect = $(self.options.statesSelect).text();
            var search = function () {
                if ($statesSelect.length > 1) {
                    self._loadCities();
                    self._loadStates();
                    clearInterval(intervalID);
                }
            };
            var intervalID = setInterval(search, 100);
        },

        _loadStates: function () {
            var self = this;
            var citiesSelect = $(self.options.citiesSelect);
            $(self.options.statesSelect).on('change',function () {
                var statesSelect = $(this).find('option').filter(':selected').text();

                if (typeof statesSelect !== 'undefined' || statesSelect.length > 1) {
                    $(citiesSelect).empty().append($('<option data-title="load" value="">' + $t('Loading...') +'</option>'));
                } else {
                    $(citiesSelect).empty().append($('<option data-title="load" value="">' + $t('-- Please select State/Province --') + '</option>'));
                }
                $('#input-option-region').val(statesSelect);

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
                            $(citiesSelect).empty().append($('<option data-title="' + $t('-- Please select --') +'" value="">' + $t('-- Please select --') +'</option>'));
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
            });
        },

        _loadCities: function () {
            $(this.options.citiesSelect).on('change',function () {
                var selectedCity = $(this).find('option').filter(':selected').text();
                if (selectedCity.length > 0){
                    $('#input-option-city').val(selectedCity);
                }
            });
        },

        _validateFormCustom: function () {
            $('#rosa-address-edit').on('click',function() {
                var inputTelephone = $("input[name='telephone']").val();
                var inputRegion = $("input[name='region']").val();
                var inputCity = $("input[name='city']").val();

                /* Validate phone */
                var number = inputTelephone.length;
                var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
                if (filter.test(inputTelephone) && number === 10) {
                    $('.rosa-mage-error').remove();
                } else {
                    $('.rosa-mage-error').remove();
                    $('.telephone').append('<div class="rosa-mage-error" style="margin: 2px; padding:' +
                        ' 2px;color:red;font-size: 1.4rem">' + $t('The phone number is incorrect, please check again.') + '</div>');
                    $('html, body').animate({scrollTop: '350px'}, 800);
                    return false;
                }

                /* Validate region*/
                if (inputRegion.length < 1 || inputRegion === '-- Please select --') {
                    $('.rosa-mage-error').remove();
                    $('.region').append('<div class="rosa-mage-error" style="margin: 2px; padding:' +
                        ' 2px;color:red;font-size: 1.4rem">' + $t('The State/Province require field.') + '</div>');
                    $('html, body').animate({scrollTop: '550px'}, 800);
                    return false;
                } else {
                    $('.rosa-mage-error').remove();
                }

                /* Validate City*/
                if (inputCity.length < 1 || inputRegion === '-- Please select --') {
                    $('.rosa-mage-error').remove();
                    $('.city').append('<div class="rosa-mage-error" style="margin: 2px; padding:' +
                        ' 2px;color:red;font-size: 1.4rem">' + $t('The City require field.') + '</div>');
                    $('html, body').animate({scrollTop: '550px'}, 800);
                    return false;
                } else {
                    $('.rosa-mage-error').remove();
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

    return $.rosalynshop.edit;
});
