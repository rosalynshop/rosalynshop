define([
    'jquery'
], function($) {
    "use strict";

    return function (widget) {
        $.widget('mage.breadcrumbs', widget, {
            _resolveCategoryCrumbs: function () {
                var result = this.options.breadcrumbsData;
                if (typeof result === 'undefined' || !result) {
                    result = this._super();
                }
                return result;
            }
        });

        return $.mage.breadcrumbs;
    }
});
