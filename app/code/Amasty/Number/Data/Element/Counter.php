<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Number
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Number\Data\Element;

class Counter extends \Magento\Framework\Data\Form\Element\AbstractElement
{


    /**
     * @param array $data
     */
    public function _construct()
    {
        parent::_construct();
        $this->setType('checkbox');
        $this->setValue('0');
    }

    public function getCanUseWebsiteValue()
    {
        return false;
    }

    public function getCanUseDefaultValue()
    {
        return false;
    }

    public function getScope()
    {
        return false;
    }
}