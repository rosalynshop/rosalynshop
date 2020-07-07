<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Controller\Adminhtml\Wards;

use Magento\Backend\App\Action;
use RosalynShop\RegionManager\Model\Wards;

/**
 * Class Delete
 * @package RosalynShop\RegionManager\Controller\Adminhtml\Wards
 */
class Delete extends Action
{
    /**
     * @var Wards
     */
    protected $_model;
    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param Wards $model
     */
    public function __construct(Action\Context $context, Wards $model)
    {
        parent::__construct($context);
        $this->_model = $model;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_model;
        $model->load($id);

        if ($model->getId()) {
            $model->delete();
            $this->messageManager->addSuccessMessage(__('Record with ID = %1 deleted!', $id));
            $this->_redirect('*/*/index');
        } else {
            $this->messageManager->addErrorMessage(__('Record with ID = %1 not found.',$id));
            $this->_redirect('*/*/index');
        }

    }
}