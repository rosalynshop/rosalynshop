define([
    'jquery',
    'Amasty_Fpc/js/charts'
], function ($, charts) {
    'use strict';

    $.widget('amasty_fpc.WarmerLog', {
        options: {},

        _create: function () {
            charts().renderSolidGaugeChart(this.options.chartId, this.options.chartData, this.options.filter);
        }
    });

    return $.amasty_fpc.WarmerLog;
});
