define([
    'jquery',
    'Amasty_Fpc/js/log/grid/subscriber',
    'Amasty_Fpc/vendor/amcharts4/charts',
    'Amasty_Fpc/vendor/amcharts4/animated'
], function ($, subscriber) {
    'use strict';

    $.widget('amasty_fpc.WarmerLogTime', {
        options: {},

        _create: function () {
            this.initialChartsStatus(this.options.chartId, this.options.chartData, this.options.filter);
        },

        initialChartsStatus: function (chartId, data, filterName) {

            this.filterName = filterName;
            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create(chartId, am4charts.PieChart);
            // Add data
            chart.data = data;

            // Set inner radius
            chart.innerRadius = am4core.percent(50);

            // Add and configure Series
            var pieSeries = chart.series.push(new am4charts.PieSeries());
            pieSeries.dataFields.value = "value";
            pieSeries.dataFields.categoryY = "category";
            pieSeries.slices.template.stroke = am4core.color("#fff");
            pieSeries.slices.template.strokeWidth = 2;
            pieSeries.slices.template.strokeOpacity = 1;
            pieSeries.labels.template.text = "{category}s";
            pieSeries.labels.template.disabled = true;
            pieSeries.ticks.template.disabled = true;
            pieSeries.slices.template.tooltipText = "{category}s : {value.formatNumber('#.0')}%[/]";

            // This creates initial animation
            pieSeries.hiddenState.properties.opacity = 1;
            pieSeries.hiddenState.properties.endAngle = -90;
            pieSeries.hiddenState.properties.startAngle = -90;

            pieSeries.slices.template.events.on("hit", function(event) {
                var filterObj = {
                    filter: this.filterName,
                    value: event.target.dataItem.categoryY
                };

                subscriber.filterGrid(filterObj); //trigger grid filter on click chart
            }, this);
        }
    });

    return $.amasty_fpc.WarmerLogTime;
});
