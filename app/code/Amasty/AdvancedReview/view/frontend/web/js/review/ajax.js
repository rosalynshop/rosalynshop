define([
    'jquery',
    'jquery/ui',
    'amrevloader'
], function ($, ui, amloader) {
     'use strict';

    $.widget('mage.amReviewAjax', {
        options: {
            toolbarSelect: '[data-amrev-js="toolbar-container"]'
        },

        _create: function () {
            var self = this;

            $(document).on(
                {
                    'click': function (event) {
                        self.sendAjaxClick(this, event);
                    },
                    'change':  function (event) {
                        self.sendAjaxChange(this);
                        event.preventDefault();
                        return false;
                    }
                },
                "[data-amreview-js='sorter'], [data-amreview-js='direction-switcher'], [data-amreview-js='filter']"
            );
        },

        sendAjaxClick: function (element, event) {
            var url = '';
            element = $(element);

            if (element.is('a')) {
                event.preventDefault();

                url = element.attr('data-href');

                if (!url) {
                    url = element.attr('href');
                }
                this.processReviews(url, true);

                return false;
            } else if (element.is('input')) {
                url = element.attr('data-href');
                this.processReviews(url, true);
            }
        },

        sendAjaxChange: function (element) {
            var url = '',
                self = this;

            element = $(element);

            if (element.is('select')) {
                var option = element.find(':selected');
                if (option) {
                    url = option.attr('data-href');
                    if (url && url.indexOf('/helpful') > 0) {
                        //disable cache for helpful sorting
                        url = url + (url.indexOf('?') === -1 ? '?' : '&') + Math.random();
                    }
                    this.processReviews(url, true);
                }
            }
        },

        processReviews: function (url, fromPages) {
            var self = this;

            if (!url) {
                return;
            }

            $(self.options.toolbarSelect).addClass('-disabled');

            $.ajax({
                url: url,
                cache: true,
                dataType: 'html',
                showLoader: false,
                loaderContext: $('#product-review-container')
            }).done(function (data) {
                $('#product-review-container').html(data).trigger('contentUpdated');
                $('[data-role="product-review"] .pages a').each(function (index, element) {
                    $(element).click(function (event) { //eslint-disable-line max-nested-callbacks
                        self.processReviews($(element).attr('href'), true);
                        event.preventDefault();
                    });
                });
            }).complete(function () {
                if (fromPages == true) { //eslint-disable-line
                    $(self.options.toolbarSelect).removeClass('-disabled');
                    $('html, body').animate({
                        scrollTop: $('#reviews').offset().top - 50
                    }, 300);
                }
            });
        }
    });

    return $.mage.amReviewAjax;
});
