var config = {
    map: {
        '*': {
            amasty_fpc_log: 'Amasty_Fpc/js/log/warmer-log-reports',
            amasty_fpc_log_time: 'Amasty_Fpc/js/log/page-time',
            amasty_fpc_report_warmed: 'Amasty_Fpc/js/crawled'
        }
    },

    shim: {
        'es6-collections': {
            deps: ['Amasty_Fpc/vendor/amcharts4/plugins/polyfill.min']
        },
        'Amasty_Fpc/vendor/amcharts4/core.min': {
            deps: ['es6-collections']
        },
        'Amasty_Fpc/vendor/amcharts4/charts': {
            deps: [
                'Amasty_Fpc/vendor/amcharts4/core.min'
            ]
        },
        'Amasty_Fpc/vendor/amcharts4/animated': {
            deps: ['Amasty_Fpc/vendor/amcharts4/core.min']
        }
    }
};
