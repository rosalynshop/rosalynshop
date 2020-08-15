define([
    'jquery',
    'underscore',
    'uiComponent'
], function ($, _, Component) {
    'use strict';
    return Component.extend({
        defaults: {
            cookieName: '',
            cookValue:  '',
            toolbarUrl: '',
            pageId:     ''
        },

        initialize: function () {
            this._super();

            var originCookieValue = $.cookie(this.cookieName);
            $.cookie(this.cookieName, window.performance.timeOrigin);
            if (this.cookieValue === null && originCookieValue === null) {
                return //we don't know cached or not now
            }
            var isHit = this.cookieValue !== originCookieValue ? 1 : 0;

            var nonCacheableBlocks = $('.mst-cache-warmer__ncb').data('ncb');

            $.ajax(this.toolbarUrl, {
                method:  'get',
                data:    {
                    isHit:              isHit,
                    pageId:             this.pageId,
                    nonCacheableBlocks: nonCacheableBlocks
                },
                success: function (response) {
                    $('body').append(response.html);
                }
            });
        }
    })
});