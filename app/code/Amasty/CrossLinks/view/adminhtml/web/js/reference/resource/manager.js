define(
    [
        'jquery',
        'mage/template',
        'mage/translate',
        'underscore',
        'jquery/ui',
        'prototype',
        'form',
        'validation'
    ], function ($) {
        'use strict';
        window.ResourceManager = {
            config: {
                catalogFieldSelector: '#reference_resource_catalog_field',
                categoryPickerContainerSelector: '.control-category-picker',
                productPickerContainerSelector: '.control-product-picker',
                referenceResourceSelector: '#reference_resource',
                referenceTypeSelector: '#reference_type',
                referenceResourceText: '',
                referenceResourceValue: null
            },

            init: function(config) {
                jQuery.extend(this.config, config || {});
                this.initEventListeners();
                this.initResourceReference();
                jQuery(this.config.catalogFieldSelector).prop('disabled', true);
            },

            setResourceValue: function(value) {
                jQuery(this.config.catalogFieldSelector).prop('disabled', false).val(value);
            },

            showResourceName: function(value) {
                jQuery(this.config.referenceResourceSelector).val(value);
                jQuery(this.config.referenceResourceSelector).prop('disabled', true);
                return this;
            },

            hidePickers: function() {
                jQuery(this.config.categoryPickerContainerSelector).hide();
                jQuery(this.config.productPickerContainerSelector).hide();
                return this;
            },

            initEventListeners: function() {
                jQuery(this.config.referenceTypeSelector).bind('change', this.initResourceReference.bind(this));
                return this;
            },

            initResourceReference: function() {
                this.hidePickers();
                var referenceType = jQuery(this.config.referenceTypeSelector).val();
                switch (parseInt(referenceType)) {
                    case 0: this.initCustomReference(); break;
                    case 1: this.initProductReference(); break;
                    case 2: this.initCategoryReference(); break;
                    default: break;
                }
                return this;
            },

            initCustomReference: function() {
                jQuery(this.config.catalogFieldSelector).prop('disabled', true);
                jQuery(this.config.referenceResourceSelector).prop('disabled', false);
            },

            initCategoryReference: function() {
                jQuery(this.config.referenceResourceSelector).val('');
                jQuery(this.config.catalogFieldSelector).prop('disabled', false);
                jQuery(this.config.referenceResourceSelector).prop('disabled', true);
                jQuery(this.config.referenceResourceSelector).val(this.config.referenceResourceText);
                jQuery(this.config.catalogFieldSelector).val(this.config.referenceResourceValue);
                jQuery(this.config.categoryPickerContainerSelector).show();
            },

            initProductReference: function() {
                jQuery(this.config.referenceResourceSelector).val('');
                jQuery(this.config.catalogFieldSelector).prop('disabled', false);
                jQuery(this.config.referenceResourceSelector).prop('disabled', true);
                jQuery(this.config.referenceResourceSelector).val(this.config.referenceResourceText);
                jQuery(this.config.catalogFieldSelector).val(this.config.referenceResourceValue);
                jQuery(this.config.productPickerContainerSelector).show();
            }
        };
    });