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

class Offset implements \Magento\Framework\Option\ArrayInterface
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

        for ($i = -12; $i <= 12; $i++){
            $v = $i > 0 ? "+$i" : $i;
            $hours = ($i==1 || $i==-1) ? '%1 hour': '%1 hours';

            $options[] = array(
                'value' => $v,
                'label' => __($hours, $v),
            );
        }
        return $options;
    }
}