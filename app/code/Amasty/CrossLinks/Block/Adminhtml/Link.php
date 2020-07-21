<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Block\Adminhtml;

/**
 * Class Link
 * @package Amasty\CrossLinks\Block\Adminhtml
 */
class Link extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Amasty_CrossLinks';
        $this->_controller = 'adminhtml_link';
        $this->_headerText = __('Cross Links Management');
        $this->_addButtonLabel = __('Add New Link');
        parent::_construct();
    }
}
