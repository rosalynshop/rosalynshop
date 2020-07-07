define([
    'jquery',
    'jquery/ui',
    'MageBig_WidgetPlus/js/owl.carousel'
], function ($) {
    'use strict';

    $.widget('magebig.owlWidget', {
        options: {
            autoplayHoverPause: true,
            smartSpeed: 750,
            rewind: true,
            navText: ['<i class="mbi mbi-chevron-left"></i>', '<i class="mbi mbi-chevron-right"></i>'],
            animateOut: 'fadeOut',
            rtl: false
        },
        _create: function() {
            var owl;

            if ($(this.element).hasClass('owl-carousel')) {
                owl = $(this.element);
            } else {
                owl = $(this.element).find('.owl-carousel');
            }

            if (owl.length) {
                if (this.options.rtl || $('body').hasClass('layout-rtl')) {
                    this.options.rtl = true;
                }

                if ($(this.element).parents('.container').length) {
                    this.options.responsiveBaseElement = '.container';
                }

                owl.on('initialized.owl.carousel', function (e) {
                    setTimeout(function () {
                        var video = owl.find('.owl-item.active video');
                        if (video.length) {
                            var paused = video[0].paused;
                            if (paused) {
                                video.get(0).play();
                            }
                        }
                    }, 2000);
                });

                owl.owlCarousel(this.options);

                owl.on('translate.owl.carousel', function (e) {
                    var video = owl.find('.owl-item video');
                    if (video.length) {
                        video.each(function () {
                            $(this).get(0).pause();
                        });
                    }
                });

                owl.on('translated.owl.carousel', function (e) {
                    var video = owl.find('.owl-item.active video');
                    if (video.length) {
                        video.get(0).play();
                    }
                });

                owl.on('dragged.owl.carousel', function (e) {
                    owl.trigger('stop.owl.autoplay');
                });
            }
        }
    });

    return $.magebig.owlWidget;
});
