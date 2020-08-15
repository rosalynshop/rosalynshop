<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Number
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Number\Model\Source;


class Reset implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = $this->toOptionArray();
        $arrayNew = [];
        foreach($array as $row) {
            $arrayNew[$row['value']] = $row['label'];
        }
        return $arrayNew;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        // magento wants at least one option to be selected
        $options[] = array(
            'value' => '',
            'label' => __('Never'),

        );
        $options[] = array(
            'value' => 'Y-m-d',
            'label' => __('Each Day'),

        );
        $options[] = array(
            'value' => 'Y-m',
            'label' => __('Each Month'),

        );
        $options[] = array(
            'value' => 'Y',
            'label' => __('Each Year'),

        );
        return $options;
    }
}