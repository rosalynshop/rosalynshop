<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Model\System;

class Store extends \Magento\Store\Model\System\Store
{
    /**
     * Retrieve store values for form
     *
     * @param bool $empty
     * @param bool $all
     * @return array
     */
    public function getStoreValuesForForm($empty = false, $all = false)
    {
        $options = parent::getStoreValuesForForm($empty, $all);

        if ($empty) {
            $options[0] = [
                'label' => __('Default'),
                'value' => 0
            ];
        }

        return $options;
    }
}
