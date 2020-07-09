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
    'uiRegistry',
    'Magento_Ui/js/form/element/region'
], function (registry, Component) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        filter: function (value, field) {
            var country = registry.get(this.parentName + '.' + 'country_id');

            if (country) {
                this._super(value, field);
            }
        }
    });
});
