<?php

namespace Aheadworks\OneStepCheckout\Plugin\Checkout;

class LayoutProcessor
{

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes $subject,
        array $jsLayout
    ) {
        if (isset($jsLayout['components']['checkout']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children']['phone-company-field-row']['children']['telephone']
        )) {

            $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
            ['phone-company-field-row']['children']['telephone']['validation'] = ['required-entry' => true, "validate-number" => true, "min_text_length" => 10, "max_text_length" => 10];
        }

        /* config: checkout/options/display_billing_address_on = payment_method */
//        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
//            ['payment']['children']['payments-list']['children']
//        )) {
//            foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
//                     ['payment']['children']['payments-list']['children'] as $key => $payment) {
//                /* telephone */
//                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
//                ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
//                ['telephone']['validation'] = ['required-entry' => true, "validate-number" => true, "min_text_length" => 10, "max_text_length" => 10];
//            }
//        }

        return $jsLayout;
    }
}