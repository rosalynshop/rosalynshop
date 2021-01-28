/**
 * abstract-mixin
 *
 * @copyright Copyright © Boolfly. All rights reserved.
 * @author    info@boolfly.com
 * @project   Megamenu
 */
define([
    'jquery'
], function () {
    'use strict';

    return function (targetModule) {
        return targetModule.extend({

            dependValues: false,

            /**
             * Add update visible and disable element
             *
             * @param value
             * @returns {Element}
             */
            updateCustomProperty: function (value) {
                if (this.dependValues !== false) {
                    if (Array.isArray(this.dependValues)) {
                        this.changeVisibleAndDisable(this.dependValues.indexOf(value) > -1);
                    } else {
                        this.changeVisibleAndDisable(value === this.dependValues);
                    }
                }

                return this;
            },

            /**
             * Trigger Update Value
             *
             * @param value
             */
            forceUpdateValue:function (value) {
                if (value) {
                    this.value.valueHasMutated();
                }
            },

            /**
             * Set Custom Visible
             *
             * @param value
             */
            setCustomVisibleProperty: function (value) {
                if (value) {
                    this.hide();
                    this.disable();
                }
            },

            /**
             * Change Visible And Disable property
             *
             * @param value
             */
            changeVisibleAndDisable: function (value) {
                if (value) {
                    this.show();
                    this.enable();
                } else {
                    this.hide();
                    this.disable();
                }

            }
        })
    }
});