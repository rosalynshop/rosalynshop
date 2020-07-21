<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Block\Adminhtml\Config\Edit\Tab;

class Category extends \Amasty\Meta\Block\Adminhtml\Widget\Form\Tab\Abstracts\Category
{
    protected function _prepareForm()
    {
        $this->_title      = __('Sub Categories');
        $this->_fieldsetId = 'cur_categories';
        $this->_prefix     = '';
        return parent::_prepareForm();
    }
}