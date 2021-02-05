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

define([
    'jquery',
    'Mageplaza_SocialLogin/js/popup',
    'mage/translate'
], function ($, socialpopup, $t) {
    "use strict";
    $.widget('mageplaza.socialpopup', socialpopup, {
        options: {
            captchaForms: [],
            captchaClientKey: '',
            captchaInvisible: false,
            isGoogleCaptcha: false
        },

        /**
         * Init Trigger Button Login
         */
        initLoginObserve: function () {
            if (this.options.captchaInvisible && ($.inArray('user_login', this.options.captchaForms) !== -1)) {
                this.loginForm.find('input').keypress(function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        $('#bnt-social-login-authentication').trigger('click');
                    }
                });
                return;
            }

            this._super();
        },

        /**
         * Init Trigger Button Create
         */
        initCreateObserve: function () {
            if (this.options.captchaInvisible && ($.inArray('user_create', this.options.captchaForms) !== -1)) {
                this.createForm.find('input').keypress(function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        $('#button-create-social').trigger('click');
                    }
                });
                return;
            }

            this._super();
        },

        /**
         * Init Trigger Button Forgot
         */
        initForgotObserve: function () {
            if (this.options.captchaInvisible && ($.inArray('user_forgotpassword', this.options.captchaForms) !== -1)) {
                this.forgotForm.find('input').keypress(function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        $('#bnt-social-login-forgot').trigger('click');
                    }
                });
                return;
            }

            this._super();
        },

        /**
         * Reload reCaptcha if enabled
         * @param type
         * @param delay
         */
        reloadCaptcha: function (type, delay) {
            this.loadApi();
            this._super();
        },

        /**
         * Login Process
         */
        processLogin: function () {
            var self = this;
            if (this.validateCaptcha(this.loginForm, 'user_login')) {
                return;
            }
            var request = this._super();
            if (typeof request !== 'undefined') {
                request.done(function (response) {
                    if (response.errors) {
                        self.resetCaptcha('user_login');
                    }
                }).fail(function () {
                    self.resetCaptcha('user_login');
                });
            } else {
                self.resetCaptcha('user_login');
            }
        },

        /**
         * Create Process
         */
        processCreate: function () {
            var self = this;
            if (this.validateCaptcha(this.createForm, 'user_create')) {
                return;
            }
            var request = this._super();
            if (typeof request !== 'undefined') {
                request.done(function (response) {
                    if (!response.success) {
                        self.resetCaptcha('user_create');
                    }
                });
            } else {
                self.resetCaptcha('user_create');
            }
        },

        /**
         * Forgot Process
         */
        processForgot: function () {
            var self = this;
            if (this.validateCaptcha(this.forgotForm, 'user_forgotpassword')) {
                return;
            }
            var request = this._super();
            if (typeof request !== 'undefined') {
                request.done(function (response) {
                    self.resetCaptcha('user_forgotpassword');
                });
            } else {
                self.resetCaptcha('user_forgotpassword');
            }
        },

        /**
         * Reset reCaptcha
         */
        resetCaptcha: function (nameProcess) {
            $.each(this.options.captchaForms, function (form, value) {
                if (value === nameProcess) {
                    grecaptcha.reset(form);
                }
            });
        },

        /**
         * Validate reCaptcha
         */
        validateCaptcha: function (form, type) {
            var formDataArray = form.serializeArray();
            var validateCaptcha = false, id;
            formDataArray.forEach(function (entry) {
                if (entry.name.includes('g-recaptcha-response') && entry.value === "") {
                    validateCaptcha = true;
                }
            });
            if (validateCaptcha) {
                form.valid();
                id = '#mageplaza-g-recaptcha-' + type;
                $(id).after("<div for='captcha' generated='true' class='mage-error' id='captcha-error' style='display: block;'>" + $t("This is a required field.") + "</div>");
                return true;
            }
            return false;
        },

        /**
         * Create reCaptcha
         */
        loadApi: function () {
            if (!this.options.isGoogleCaptcha) {
                return;
            }

            var self = this,
                isInvisible = this.options.captchaInvisible;

            window.recaptchaOnload = function () {
                $.each(self.options.captchaForms, function (form, value) {
                    var target = '',
                        parameters = {
                            'sitekey': self.options.captchaClientKey,
                            'size': isInvisible ? 'invisible' : 'normal'
                        };

                    switch (value) {
                        case 'user_login':
                            target = isInvisible ? 'bnt-social-login-authentication' : 'mageplaza-g-recaptcha-user_login';
                            if (isInvisible) {
                                parameters.callback = self.processLogin.bind(self);
                            }
                            break;
                        case 'user_create':
                            target = isInvisible ? 'button-create-social' : 'mageplaza-g-recaptcha-user_create';
                            if (isInvisible) {
                                parameters.callback = self.processCreate.bind(self);
                            }
                            break;
                        case 'user_forgotpassword':
                            target = isInvisible ? 'bnt-social-login-forgot' : 'mageplaza-g-recaptcha-user_forgotpassword';
                            if (isInvisible) {
                                parameters.callback = self.processForgot.bind(self);
                            }
                            break;
                    }
                    grecaptcha.render(target, parameters);
                });
            };

            require(['mpReCaptcha']);

            this.options.isGoogleCaptcha = false;
        }
    });

    return $.mageplaza.socialpopup;
});