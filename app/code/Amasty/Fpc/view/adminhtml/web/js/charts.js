define([
    'jquery',
    'Amasty_Fpc/js/log/grid/subscriber',
    'Amasty_Fpc/vendor/amcharts4/core.min',
    'Amasty_Fpc/vendor/amcharts4/charts',
    'Amasty_Fpc/vendor/amcharts4/animated'
], function ($, subscriber) {
    'use strict';

    $.widget('amasty_fpc.Charts', {
        options: {},

        renderSolidGaugeChart: function (containerId, data, filterName) {
            this.filterName = filterName;

            am4core.useTheme(am4themes_animated);

            var chart = am4core.create(containerId, am4charts.RadarChart);

            chart.data = data;

            chart.startAngle = -90;
            chart.endAngle = 180;
            chart.innerRadius = am4core.percent(20);
            chart.numberFormatter.numberFormat = "#.#'%'";

            var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "category";
            categoryAxis.dataFields.categoryLabel = "label"; //custom dataField
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.grid.template.strokeOpacity = 0;
            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.fontSize = 10;
            categoryAxis.renderer.labels.template.fontWeight = 500;
            categoryAxis.renderer.labels.template.adapter.add("fill", function(fill, target) {
                return (target.dataItem.index >= 0) ? chart.colors.getIndex(target.dataItem.index) : fill;
            });
            categoryAxis.renderer.labels.template.adapter.add("text", function() {
                return "{category} {categoryLabel}";
            });
            categoryAxis.renderer.minGridDistance = 10;

            var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.grid.template.strokeOpacity = 0;
            valueAxis.min = 0;
            valueAxis.max = 100;
            valueAxis.strictMinMax = true;
            valueAxis.renderer.labels.template.fontSize = 10;

            var series1 = chart.series.push(new am4charts.RadarColumnSeries());
            series1.dataFields.valueX = "full";
            series1.dataFields.categoryY = "category";
            series1.clustered = false;
            series1.columns.template.fill = new am4core.InterfaceColorSet().getFor("alternativeBackground");
            series1.columns.template.fillOpacity = 0.08;
            series1.columns.template.cornerRadiusTopLeft = 20;
            series1.columns.template.strokeWidth = 0;
            series1.columns.template.radarColumn.cornerRadius = 20;

            var series2 = chart.series.push(new am4charts.RadarColumnSeries());
            series2.dataFields.valueX = "value";
            series2.dataFields.categoryY = "category";
            series2.dataFields.categoryLabel = "label"; //custom dataField
            series2.clustered = false;
            series2.columns.template.strokeWidth = 0;
            series2.columns.template.hoverable = true;
            series2.columns.template.tooltipText = "{category} {categoryLabel}: [bold]{value}[/]";
            series2.columns.template.radarColumn.cornerRadius = 20;
            series2.columns.template.cursorOverStyle = am4core.MouseCursorStyle.pointer;
            series2.columns.template.events.on("hit", function(event) {
                var filterObj = {
                    filter: this.filterName,
                    value: event.target.dataItem.categoryY
                };

                subscriber.filterGrid(filterObj); //trigger grid filter on click chart
            }, this);
            series2.columns.template.adapter.add("fill", function(fill, target) {
                return chart.colors.getIndex(target.dataItem.index);
            });
        },
    });

    return $.amasty_fpc.Charts;
});
