<?php

namespace Zemi\ShippingPrice\Plugin;

class ItemPriceCalculator
{
    public function aroundGetShippingPricePerItem(
        \Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $basePrice,
        $freeBoxes
    ) {
        $shippingPrice = 0;
        $weight = $request->getPackageWeight();
        if ($weight <= 0.25 ) {
            $shippingPrice = $request->getPackageQty() * $basePrice - $freeBoxes * $basePrice;
        } elseif ($weight > 0.25 && $weight <= 0.5) {
            $shippingPrice = $basePrice + 3000;
        } elseif ($weight > 0.5 && $weight <= 1) {
            $shippingPrice = $basePrice + 8000;
        } elseif ($weight > 1 && $weight <= 2) {
            $shippingPrice = $basePrice + 12000;
        } elseif ($weight > 2 && $weight <= 3) {
            $shippingPrice = $basePrice + 18000;
        } elseif ($weight > 3 && $weight <= 5) {
            $shippingPrice = $basePrice + 6000 * (floor($weight/0.5));
        } else if ($weight > 5 && $weight <= 10 ) {
            $shippingPrice = $basePrice + 4000 * (floor($weight/0.5));
        } elseif ($weight > 10 && $weight <= 100) {
            $shippingPrice = $basePrice + 3500 * (floor($weight/0.5));
        } elseif ($weight > 100) {
            $shippingPrice = $basePrice + 3000 * (floor($weight/0.5));
        }
        return $shippingPrice ;
    }
}
