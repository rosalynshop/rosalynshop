define([
    "jquery",
    "jquery/ui",
    "Amasty_Base/vendor/slick/slick.min",
    "Amasty_AdvancedReview/vendor/fancybox/jquery.fancybox.min"
], function ($, ui, slick, fancybox) {
    'use strict';

    $.widget('mage.amReview', {
        options: {
            responsive: [
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        },
        imageContainer: '[data-amreview-js="review-images"]',

        _create: function () {
            $('.amreview-description-wrap').on('click', '[data-amreview-js="readmore"]', function () {
                $(this).prev().removeClass('amreview-showless');
                $(this).remove();
            });

            $('[data-amreview-js="show-more"]').on('click', function () {
                $('[data-amreview-js="percent"]').toggle();
                $('[data-amreview-js="summary-details"]').toggle();
            });

            // Fix problem with slick init
            $('#tab-label-reviews').on('click', function () {
                $('.amreview-images.slick-initialized').slick('setPosition');
            });

            this.initSlider();
        },

        initSlider: function () {
            var self = this,
                slidesToShow = $(window).width() > 768 ? self.options.slidesToShow : 1,
                $imageContainer = $(self.imageContainer);

            if ($imageContainer.length) {
                $.each($imageContainer, function () {
                    var $element = $(this);

                    $element.find('a').fancybox({
                        loop: true,
                        toolbar: false,
                        baseClass: 'amrev-fancybox-zoom'
                    });

                    if ($element.find('img').length > slidesToShow && self.options.slidesToShow) {
                        $element.slick(self.options);
                        $element.slick('resize');
                    }
                });
            }
        }
    });

    return $.mage.amReview;
});
