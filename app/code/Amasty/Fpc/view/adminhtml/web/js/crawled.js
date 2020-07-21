define([
    'jquery',
    'Amasty_Fpc/js/efficiency',
    'Amasty_Fpc/vendor/amcharts4/charts',
    'mage/translate'
], function ($, efficiency) {
    'use strict';

    $.widget('amasty_fpc.WarmerReport', {
        options: {},
        reloadUrl: '',
        chartId: '',
        dayConst: 1,
        weekConst: 2,
        monthConst: 3,

        _create: function () {
            this.reloadUrl = this.options.reloadUrl;
            this.chartId = this.options.chartId;
            this.addEvents();
            this.initialChartsStatus(this.chartId, this.options.chartData);
        },

        addEvents: function () {
            $('[data-amfpc-js="warmed-btn-reload"]').on('click', this.reloadChart.bind(this));
        },

        reloadChart: function () {
            var filterValue = $('[data-amfpc-js="warmed-filter"]').val(),
                self = this,
                reloadUrl = this.reloadUrl.slice(0, -1);

            $.ajax({
                url: reloadUrl,
                type: 'GET',
                data: {
                    key_warm: filterValue
                },
                success: function (data) {
                    if (filterValue > 1) data = self.prepareData(data, filterValue);
                    self.initialChartsStatus(self.chartId, data);
                }
            })
        },

        prepareData: function (data, filterValue) {
            const week = 7;
            var period,
                fields = ['hits', 'misses', 'warmed', 'visited_at'];

            if (+filterValue === this.monthConst) {
                period = week;
            } else {
                return data;
            }

            if (data.length < period) period = data.length;

            return efficiency().getPrepareData(data, period, fields, false);
        },

        initialChartsStatus: function (chartId, data) {
            this.chart = am4core.create(chartId, am4charts.XYChart);
            this.chart.data = data;
            var categoryAxis = this.chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "visited_at";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 20;
            categoryAxis.renderer.cellStartLocation = 0.1;
            categoryAxis.renderer.cellEndLocation = 0.9;

            var  valueAxis = this.chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.min = 0;

            this.createSeries("hits", $.mage.__('Hit'), false);
            this.createSeries("misses", $.mage.__('Miss'), true);
            this.createSeries("warmed", $.mage.__('Warmed'), false);

            this.chart.legend = new am4charts.Legend();
        },

        createSeries: function (field, name, stacked) {
            var series = this.chart.series.push(new am4charts.ColumnSeries());

            series.dataFields.valueY = field;
            series.dataFields.categoryX = "visited_at";
            series.name = name;
            series.columns.template.tooltipText = "{name}: [bold]{valueY}[/]";
            series.stacked = stacked;
            series.columns.template.width = am4core.percent(95);
        }
    });

    return $.amasty_fpc.WarmerReport;
});
