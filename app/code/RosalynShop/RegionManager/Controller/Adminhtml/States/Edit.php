<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Controller\Adminhtml\States;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package RosalynShop\RegionManager\Controller\Adminhtml\States
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
        $this->_view->getPage()->getConfig()->getTitle()->prepend(isset($this->getRequest()->getParams()["id"]) ? __('Edit States') : __('Add States'));
        $this->_view->renderLayout();
    }
}