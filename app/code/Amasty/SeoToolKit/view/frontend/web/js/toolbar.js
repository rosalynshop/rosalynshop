define([
    'jquery',
    'mage/translate'
], function ($, $tr) {
    'use strict';

    $.widget('mage.amSeoToolbar', {
        contentSelector: '[data-js="amskit-content"]',
        closeSelector: '[data-js="amskit-close"]',
        showMoreSelector: '[data-js="amskit-showmore"]',
        showButtonSelector: '[data-js="amskit-showbutton"]',
        maxVisibleImgs: 2,

        _create: function () {
            var self = this,
                toolbarBox = this.element;

            toolbarBox.on('click', function () {
                self.showToolbar(toolbarBox);
            });

            $(self.closeSelector).on('click', function (e) {
                self.hideToolbar(this, e);
            });

            self.showMore();
        },

        showToolbar: function (element) {
            var self = this;

            if (!element.hasClass('-open')) {
                element.addClass('-open');
                element.find(self.contentSelector).show();
                element.children(':first').addClass('-open');
            }
        },

        hideToolbar: function (el, e) {
            var self = this,
                element = $(el).parent();

            if (element.hasClass('-open')) {
                e.stopPropagation();
                element.removeClass('-open');
                element.next(self.contentSelector).hide();
                element.parent().removeClass('-open');
            }
        },

        showMore: function () {
            var self = this,
                count,
                buttonTemplate,
                urls = $(self.showMoreSelector).children();

            if (urls.length > self.maxVisibleImgs) {
                count = urls.length - self.maxVisibleImgs;
                buttonTemplate = '<div class="amskit-separator-wrap" data-js="amskit-showbutton">' +
                    '<div class="amskit-button">' + $tr('other ') + count + '</div></div>';

                $(self.showMoreSelector).children(':nth-child(' + self.maxVisibleImgs + ')').after(buttonTemplate);

                $(self.showButtonSelector).on('click', function () {
                    $(self.showButtonSelector).off();
                    $(self.showButtonSelector).remove();
                });
            }
        }
    });

    return $.mage.amSeoToolbar;
});
