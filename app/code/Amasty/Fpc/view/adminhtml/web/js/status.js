define([
    'uiComponent',
    'jquery',
    'ko',
    'Amasty_Fpc/vendor/amcharts4/charts',
    'Amasty_Fpc/vendor/amcharts4/animated'
], function(Component, $, ko) {

    return Component.extend({


        initialize: function () {
            this._super();
            var data = this.data;

            this.info = ko.observableArray();
            this.addEvents();
            this.initGraph(data);
        },

        initGraph: function (data) {
            this.addPercentData(data);
            this.info.removeAll();
            this.info(data);
            this.initialChartsStatus(data);
        },

        addEvents: function () {
            $('[data-amfpc-js="status-btn-reload"]').on('click', this.reloadChart.bind(this));
        },

        reloadChart: function () {
            var filterValue = $('[data-amfpc-js="status-filter"]').val(),
                self = this,
                reloadUrl = this.reloadUrl.slice(0, -1);

            $.ajax({
                url: reloadUrl,
                type: 'GET',
                data: {
                    key_status: filterValue
                },
                success: function (data) {
                    self.initGraph(data);
                }
            });
        },

        addPercentData: function (data) {
            var total = 0;

            data.forEach(function (item) {
               total += Number(item.count);
            });

            data.map(function (item) {
               item.percent = item.count / total;
               return item;
            });
        },

        initialChartsStatus: function (data) {
            am4core.ready(function() {

                // Themes begin
                am4core.useTheme(am4themes_animated);
                // Themes end

                // Create chart instance
                var chart = am4core.create("amfpc-chart-status", am4charts.PieChart);
                // Add data
                chart.data = data;

                // Set inner radius
                chart.innerRadius = am4core.percent(50);

                // Add and configure Series
                var pieSeries = chart.series.push(new am4charts.PieSeries());
                pieSeries.dataFields.value = "percent";
                pieSeries.dataFields.category = "status";
                pieSeries.slices.template.stroke = am4core.color("#fff");
                pieSeries.slices.template.strokeWidth = 2;
                pieSeries.slices.template.strokeOpacity = 1;

                // This creates initial animation
                pieSeries.hiddenState.properties.opacity = 1;
                pieSeries.hiddenState.properties.endAngle = -90;
                pieSeries.hiddenState.properties.startAngle = -90;
            });
        }
    });
});