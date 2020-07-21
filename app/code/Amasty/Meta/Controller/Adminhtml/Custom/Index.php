<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Controller\Adminhtml\Custom;

class Index extends \Amasty\Meta\Controller\Adminhtml\Config\Index
{
    protected $_title = 'Meta Tags Template (URLs)';
    protected $_isCustom = true;

    public function execute()
    {
        $this->_blockName = 'custom';
        parent::execute();
    }
}