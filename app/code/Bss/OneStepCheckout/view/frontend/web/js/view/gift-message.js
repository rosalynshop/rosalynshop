/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'ko',
    'Magento_GiftMessage/js/view/gift-message',
    'Bss_OneStepCheckout/js/model/gift-message'
], function (ko, Component, GiftMessage) {
        'use strict';

        return Component.extend({
            formBlockVisibility: null,
            model: {},
            isLoading: ko.observable(false),

            /**
             * Component init
             */
            initialize: function () {
                var self = this,
                    model;
                this._super();
                this.itemId = this.itemId || 'orderLevel';
                this.model = new GiftMessage(this.itemId);
                this.model.afterSubmit = function () {
                    self.hideFormBlock();
                    self.resultBlockVisibility(false);
                    self.isLoading(false);
                };
            },

            /**
             * Submit options
             */
            submitOptions: function () {
                this.isLoading(true);
                this._super();
                this.model.getObservable('alreadyAdded')(true);
            },

            /**
             * Delete options
             */
            deleteOptions: function () {
                this.isLoading(true);
                this._super();
                this.model.getObservable('alreadyAdded')(false);
            }
        });
    });
