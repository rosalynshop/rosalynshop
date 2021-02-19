/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SocialLoginPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Customer/js/model/customer',
        'mage/translate',
        'Magento_Ui/js/modal/modal',
        'rjsResolver'
    ],
    function ($, ko, Component, customer, $t, modal, resolver) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Mageplaza_SocialLoginPro/authentication'
            },

            /**
             * Init
             */
            initialize: function () {
                var self = this;
                this._super();
                this.popup = $('#social-login-popup');
                resolver(function () {
                    var options = {
                        type: 'popup',
                        responsive: true,
                        innerScroll: true,
                        title: 'Popup',
                        modalClass: 'osc-social-login-popup',
                        buttons: []
                    };
                    modal(options, self.popup);
                    $("button[data-trigger*='authentication-mixin']").on('click', function () {
                        self.popup.modal('openModal');
                        self.popup.socialpopup('loadApi');
                    });
                });
                return this;
            },
            /** Is login form enabled for current customer */
            isActive: function () {
                return !customer.isLoggedIn();
            }
        });
    }
);
