define([
    'jquery',
    'underscore',
    'ko',
    'uiComponent'
], function ($, _, ko, Component) {
    'use strict';
    
    return Component.extend({
        defaults: {
            template: 'Mirasvit_CacheWarmer/component/info',
            
            cacheType:    '',
            cacheTtl:     0,
            fillHistory:  [],
            fillRates:    {
                inCache: 0,
                pending: 0,
                total:   1
            },
            coverageRate: 0,
            history:      [],
            historyFrom:  '',
            historyTo:    ''
        },
        
        initialize: function () {
            this._super();
            this.initHistory();
            
            return this;
        },
        
        getInCacheRate: function () {
            return Math.round(this.fillRates.inCache / this.fillRates.total * 100) + "%";
        },
        
        getPendingRate: function () {
            return Math.round((this.fillRates.total - this.fillRates.inCache) / this.fillRates.total * 100) + "%";
        },
        
        getCoverageRate: function () {
            if (this.coverageRate === 0) {
                return '0%';
            }
            return Math.round(this.coverageRate) + "%";
        },
        
        initHistory: function () {
            var points = [];
            var len = _.keys(this.fillHistory).length;
            
            if (len > 1) {
                this.historyFrom = _.first(_.keys(this.fillHistory));
                this.historyTo = _.last(_.keys(this.fillHistory));
                
                points = ['0,100'];
                
                var i = 0;
                _.each(this.fillHistory, function (rate) {
                    var x = Math.round(100 / (len - 1) * i, 3);
                    var y = 100 - rate;
                    points.push(x + "," + y);
                    i++;
                });
                
                points.push('100,100');
            }
            
            this.history = points.join(' ');
        }
    });
});
