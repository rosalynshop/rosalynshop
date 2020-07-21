<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Meta\Controller\Adminhtml\Config;

class Index extends \Amasty\Meta\Controller\Adminhtml\Config
{
    protected $_title = 'Meta Tags Template (Categories)';
    protected $_modelName = 'Config';
    protected $_blockName = 'config';
    protected $_isCustom = false;

    public function execute()
    {
        $this->_view->loadLayout();

        $this->_view->getPage()->getConfig()->getTitle()->prepend($this->_title);

        $this->_setActiveMenu('cms/amseotoolkit/ammeta');
        $block =  $this->_view->getLayout()->createBlock(
            'Amasty\Meta\Block\Adminhtml\\' . $this->_modelName,
            '',
            [
                'data' =>
                    [
                        'is_custom' => $this->_isCustom,
                        'title' => $this->_title
                    ]
            ]
        );
        $this->_addContent($block);
        $this->_view->renderLayout();

    }
}