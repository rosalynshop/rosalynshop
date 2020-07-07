/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'underscore',
    'Magento_Ui/js/grid/listing',
    "jquery",
    "MageBig_WidgetPlus/js/owl.carousel"
], function (ko, _, Listing, $) {
    'use strict';

    return Listing.extend({
        defaults: {
            additionalClasses: '',
            filteredRows: {},
            limit: 5,
            listens: {
                elems: 'filterRowsFromCache',
                '${ $.provider }:data.items': 'filterRowsFromServer'
            }
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.filteredRows = ko.observable();
            this.initProductsLimit();
            this.hideLoader();
        },

        /**
         * Initialize product limit
         * Product limit can be configured through Ui component.
         * Product limit are present in widget form
         *
         * @returns {exports}
         */
        initProductsLimit: function () {
            if (this.source['page_size']) {
                this.limit = this.source['page_size'];
            }

            return this;
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Listing} Chainable.
         */
        initObservable: function () {
            this._super()
                .track({
                    rows: []
                });

            return this;
        },

        /**
         * Sort and filter rows, that are already in magento storage cache
         *
         * @return void
         */
        filterRowsFromCache: function () {
            this._filterRows(this.rows);
        },

        /**
         * Sort and filter rows, that are come from backend
         *
         * @param {Object} rows
         */
        filterRowsFromServer: function (rows) {
            this._filterRows(rows);
        },

        /**
         * Filter rows by limit and sort them
         *
         * @param {Array} rows
         * @private
         */
        _filterRows: function (rows) {
            this.filteredRows(_.sortBy(rows, 'added_at').reverse().slice(0, this.limit));
        },

        /**
         * Can retrieve product url
         *
         * @param {Object} row
         * @returns {String}
         */
        getUrl: function (row) {
            return row.url;
        },

        /**
         * Get product attribute by code.
         *
         * @param {String} code
         * @return {Object}
         */
        getComponentByCode: function (code) {
            var elems = this.elems() ? this.elems() : ko.getObservable(this, 'elems'),
                component;

            component = _.filter(elems, function (elem) {
                return elem.index === code;
            }, this).pop();

            return component;
        },

        /**
         * Init slider
         */
        sliderInit: function () {
            var rtl = false;
            if ($('body').hasClass('layout-rtl') || $('body').hasClass('rtl')) {
                rtl =  true;
            }
            setTimeout(function () {
                $('.recently-viewed .owl-carousel').owlCarousel({
                    "autoplay"          : false,
                    "autoplayTimeout"   : 5000,
                    "items"             : 8,
                    "margin"            : 30,
                    "rewind"            : true,
                    "nav"               : true,
                    "navText"           : ['<i class="mbi mbi-chevron-left"></i>', '<i class="mbi mbi-chevron-right"></i>'],
                    "dots"              : false,
                    "responsive"        : {"0":{"items":3},"576":{"items":3},"768":{"items":4},"992":{"items":6},"1200":{"items":8}},
                    "rtl"               : rtl
                });
            }, 100);
        }
    });
});
