<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Block\Adminhtml\Config\Edit\Tab;

class Product extends \Amasty\Meta\Block\Adminhtml\Widget\Form\Tab\Abstracts\Product
{
    protected function _prepareForm()
    {
        $this->_title      = __('Current Products');
        $this->_fieldsetId = 'cur_products';

        return parent::_prepareForm();
    }
}