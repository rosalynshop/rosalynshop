/**
 *  Warmer Log Grid Ui Component
 */
define([
    'jquery',
    'Magento_Ui/js/grid/listing',
    'uiRegistry',
    'Amasty_Fpc/js/log/grid/subscriber'
], function ($, Listing, registry, subscriber) {
    'use strict';

    return Listing.extend({
        defaults: {
            imports: {
                filtersPath: '${ $.parentName }.listing_top.listing_filters'
            }
        },

        initialize: function () {
            this._super();

            subscriber.filterGrid.subscribe(function(filterData) {
                this.filterGrid(filterData.filter, filterData.value)
            }, this);
        },

        filterGrid: function (filter, value) {
            registry.get(this.imports.filtersPath, function (element) {
                element.set(filter, value).apply();
            }, this);
        }
    });
});
