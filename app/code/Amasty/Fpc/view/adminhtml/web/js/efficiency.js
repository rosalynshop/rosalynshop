define([
    'uiComponent',
    'jquery',
    'ko',
    'uiRegistry',
    'Amasty_Fpc/vendor/amcharts4/charts',
    'Amasty_Fpc/vendor/amcharts4/animated',
    'mage/translate'
], function(Component, $, ko, registry) {

    return Component.extend({

        gridType: 1,
        dayConst: 1,
        weekConst: 2,
        monthConst: 3,

        initialize: function () {
            this._super();
            this.addEvents();
        },

        init: function () {
            var data = registry.get('index = amasty_fpc_report_listing').source.data.items;

            if (this.gridType() > this.dayConst) data = this.prepareData(data);
            this.initialChartsStatus(data);
        },

        prepareData: function (data) {
            const week = 7;
            var period,
                fields = ['hit_response_time', 'hits', 'miss_response_time', 'misses', 'response_time', 'visits', 'visited_at'];

            if (+this.gridType() === this.monthConst) {
                period = week;
            } else {
                return data;
            }

            if (data.length < period) period = data.length;

            return this.getPrepareData(data, period, fields, true);
        },

        getPrepareData: function (data, period, fields, isAverage) {
            var dataIndex = 0,
                prepareData = [],
                dateField = 'visited_at',
                self = this,
                realIndex = 0,
                field;

            data.forEach(function(item, index) {
                var newPeriod = false;

                for (var fieldIndex = 0; fieldIndex < fields.length; fieldIndex++) {
                    field = fields[fieldIndex];
                    if (field === dateField) {
                        realIndex = index + 1;
                        if (index === data.length - 1 || realIndex % period === 1 || realIndex % period === 0) {
                            prepareData[dataIndex][field] = self.changePrepareDate(prepareData[dataIndex][field], item[field]);
                        }
                        continue;
                    } else if (field === dateField) {
                        continue;
                    }

                    if (index % period === 0 && fieldIndex === 0 && index !== 0) {
                        newPeriod = true;
                        dataIndex++;
                    }
                    if (!prepareData[dataIndex]) prepareData[dataIndex] = {};

                    if (index === 0 || newPeriod) {
                        prepareData[dataIndex][field] = +item[field];
                    } else {
                        prepareData[dataIndex][field] += +item[field];
                    }
                }
            });

            if (isAverage) this.getAverageData(prepareData, period);

            return prepareData;
        },

        getAverageData: function (data, period) {
            data.map(function (item) {
                for (var key in item) {
                    if (key === 'visits' || key === 'visited_at') continue;
                    item[key] = (item[key] / period).toFixed(2);
                }
                return item;
            });

            return data;
        },

        changePrepareDate: function (field, date) {
            if(!field) return date;

            return field + ' - ' + date;
        },

        initObservable: function () {
            this._super()
                .observe(['gridType']);

            return this;
        },

        addEvents: function () {
            $('[data-amfpc-js="btn-reload"]').on('click', this.reloadGrid.bind(this));
        },

        reloadGrid: function () {
            this.gridType($('[data-amfpc-js="interval-filter"]').val());
        },

        initialChartsStatus: function (data) {
            var self = this;

            am4core.ready(function() {

                self.chart = am4core.create("chart-efficiency", am4charts.XYChart);
                self.chart.data = data;
                self.createAxys('visited_at');

                var valueAxis = self.chart.yAxes.push(new am4charts.ValueAxis());
                valueAxis.min = 0;

                self.createSeries("response_time",  $.mage.__('Response Time / ms'), false);
                self.createSeries("hit_response_time", $.mage.__('Hit Response Time / ms'), false);
                self.createSeries("miss_response_time", $.mage.__('Miss Response Time / ms'), false);

                var percentValueAxis = self.createPercentAxis();

                self.createPercentSeries("hits", $.mage.__('Hits / %'), false, true, percentValueAxis);
                self.createPercentSeries("misses", $.mage.__('Misses / %'), false, true,  percentValueAxis);

                self.chart.legend = new am4charts.Legend();

            });
        },

        createPercentAxis: function () {
            var percentValueAxis = this.chart.yAxes.push(new am4charts.ValueAxis());

            percentValueAxis.renderer.opposite = true;
            percentValueAxis.min = 0;
            percentValueAxis.max = 100;
            percentValueAxis.strictMinMax = true;

            return percentValueAxis;
        },

        createAxys: function (category) {
            var categoryAxis = this.chart.xAxes.push(new am4charts.CategoryAxis());

            categoryAxis.dataFields.category = category;
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 70;
            categoryAxis.renderer.cellStartLocation = 0.1;
            categoryAxis.renderer.cellEndLocation = 0.9;

            return categoryAxis;
        },

        createPercentSeries: function (field, name, stacked, isHide, axis) {
            var percentSeries = this.chart.series.push(new am4charts.ColumnSeries());

            percentSeries.dataFields.valueY = field;
            percentSeries.dataFields.categoryX = "visited_at";
            percentSeries.name = name;
            percentSeries.columns.template.tooltipText = "{name}: {valueY.formatNumber('#.0')}%[/]";
            percentSeries.yAxis = axis;
            percentSeries.stroke = new am4core.InterfaceColorSet().getFor("alternativeBackground");
            percentSeries.hidden = isHide;
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
});