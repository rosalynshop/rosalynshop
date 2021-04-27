define([
    'ko',
    'underscore',
    'Magento_Ui/js/lib/spinner',
    'rjsResolver',
    'uiLayout',
    'Magento_Ui/js/grid/listing',
    'uiRegistry'
], function (ko, _, loader, resolver, layout, Listing, registry) {
    'use strict';

    return Listing.extend({
        initObservable: function () {
            this._super()
                .track({
                    totals: {}
                });

            return this;
        },
        onDataReloaded: function () {
            this._super();
            registry.async("index = efficiency")(function (efficiency) {
                efficiency.init()
            }.bind(this));
        }
    });
});
