define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (initialize) {
        return wrapper.wrap(initialize, function (originalAction) {
            $('[data-role="product-review-form"]').on('submit', function () {
                if ($(this).find('.mage-error:visible').length == 0) {
                    $('[data-role="product-review-form"] .action.submit').prop('disabled', true);
                }
            });

            return originalAction();
        });
    };
});
