/**
 * Generation loader element for node element type 'block'
 *
 * @return loader node element
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return {
        options: {
            wrapper: '[data-amload-js="container"]',
            blockSelect: '[data-amload-js="block"]',
            containerTemplate: '<figure class="am-load-block" data-amload-js="block"></figure>',
            overlayTemplate: '<div class="am-dots"></div>',
            dotTemplate: '<span class="am-dot"></span>',
            dotsQty: 8
        },
        nodeElem: {},

        init: function (wrapper) {
            var self = this,
                blockNode = $(wrapper).find(self.options.blockSelect);

            self.options.wrapper = wrapper;
            if (!blockNode.length) {
                self._create();
                self._addElem();
            }

            if (blockNode.length) {
                blockNode.show();
            }
        },

        stop: function (wrapper) {
            var self = this;

            $(wrapper).find(self.options.blockSelect).hide();
        },

        start: function (wrapper) {
         var self = this;

            $(wrapper).find(self.options.blockSelect).show();
        },

        _create: function () {
            var self = this;

            if (!self.nodeElem.length) {
                var container = $(self.options.containerTemplate),
                    overlay = $(self.options.overlayTemplate);

                container.append(overlay);

                for (var i = 0; i < self.options.dotsQty; i++) {
                    overlay.append($(self.options.dotTemplate));
                }

                self.nodeElem = container;
            }
        },

        _addElem: function () {
            var self = this;

            $(self.options.wrapper).append(self.nodeElem);
        }
    }
});
