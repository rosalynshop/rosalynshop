/**
 * requirejs-config
 *
 * @copyright Copyright © Boolfly. All rights reserved.
 * @author    info@boolfly.com
 * @project   Core
 */
var config = {
    map: {
        '*': {
            slick: 'Boolfly_Base/js/slick.min',
            magnificPopup: 'Boolfly_Base/js/jquery.magnific-popup.min',
            'jquery/lazy': 'Boolfly_Base/js/jquery.lazy.min',
            'jquery/lazy/plugins': 'Boolfly_Base/js/jquery.lazy.plugins.min',
            lazyload: 'Boolfly_Base/js/verlok/lazyload.min',
            'intersection-observer': 'Boolfly_Base/js/verlok/intersection-observer.amd.min',
            nestable: 'Boolfly_Base/js/jquery.nestable.min',
            'mage/dataPost': 'Boolfly_Base/js/mage/dataPost'
        }
    },
    shim: {
        slick: {
            deps: ['jquery']
        },
        magnificPopup: {
            deps: ['jquery']
        },
        'jquery/lazy': {
            deps: ['jquery']
        },
        'jquery/lazy/plugins': {
            deps: ['jquery', 'jquery/lazy']
        },
        lazyload: {
            exports: 'LazyLoad'
        },
        'intersection-observer': {
            exports: 'IntersectionObserver'
        },
        nestable: {
            deps: ['jquery']
        }
    },
    config: {
        mixins: {
            'Magento_Ui/js/form/element/abstract': {
                'Boolfly_Base/js/form/element/abstract-mixin': true
            }
        }
    }
};