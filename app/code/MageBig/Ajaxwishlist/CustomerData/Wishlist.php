<?php

namespace MageBig\Ajaxwishlist\CustomerData;

/**
 * Wishlist section
 */
class Wishlist extends \Magento\Wishlist\CustomerData\Wishlist
{
    /**
     * Create button label based on wishlist item quantity
     *
     * @param int $count
     * @return \Magento\Framework\Phrase|null
     */
    protected function createCounter($count)
    {
        if ($count > 1) {
            return __('%1', $count);
        } elseif ($count == 1) {
            return __('1');
        }
        return null;
    }
}
