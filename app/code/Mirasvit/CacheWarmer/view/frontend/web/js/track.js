define([
    'jquery',
    'underscore',
    'uiComponent'
], function ($, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            url:         "",
            cookieName:  "",
            cookieValue: ""
        },

        initialize: function () {
            this._super();

            var originCookieValue = $.cookie(this.cookieName);

            $.cookie(this.cookieName, window.performance.timeOrigin);

            if (this.cookieValue === null && originCookieValue === null) {
                return //we don't know cached or not now
            }
            var uri = window.location.href;
            var ttfb = window.performance.timing.responseStart - window.performance.timing.fetchStart;
            var isHit = this.cookieValue !== originCookieValue ? 1 : 0;

            $.ajax(this.url, {
                method: 'get',
                data:   {
                    uri:   uri,
                    ttfb:  ttfb,
                    isHit: isHit
                }
            });
        }
    })
});