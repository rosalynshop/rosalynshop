define([
    'jquery',
    'underscore',
    'uiComponent'
], function ($, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            url: ""
        },

        initialize: function () {
            this._super();

            var ttfb = window.performance.timing.responseStart - window.performance.timing.fetchStart;
            $.ajax(this.url, {
                method: 'get',
                data:   {
                    ttfb:  ttfb
                }
            });
        }
    })
});