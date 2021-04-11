<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Controller\Adminhtml\Wards;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Zemi\RegionManager\Controller\Adminhtml\Wards
 */
class Edit extends Action
{
    /**
     * Edit constructor.
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            isset($this->getRequest()->getParams()["id"]) ? __('Edit Wards') : __('Add Wards')
        );
        $this->_view->renderLayout();
    }
}