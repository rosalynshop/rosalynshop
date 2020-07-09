var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Bss_OneStepCheckout/js/model/place-order-mixin': true,
                'Magento_CheckoutAgreements/js/model/place-order-mixin': false
            },
            'Magento_Checkout/js/model/step-navigator': {
                'Bss_OneStepCheckout/js/model/step-navigator-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Magento_CheckoutAgreements/js/model/set-payment-information-mixin': false,
                'Bss_OneStepCheckout/js/model/set-payment-information-mixin': true
            }
        }
    }
};
