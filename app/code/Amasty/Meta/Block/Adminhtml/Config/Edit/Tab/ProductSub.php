<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Block\Adminhtml\Config\Edit\Tab;

class ProductSub extends \Amasty\Meta\Block\Adminhtml\Widget\Form\Tab\Abstracts\Product
{
    protected function _prepareForm()
    {
        $this->_title      = __('Products In Sub Categories');
        $this->_fieldsetId = 'sub_products';
        $this->_prefix     = 'sub_';

        return parent::_prepareForm();
    }
}