<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Controller\Adminhtml\States;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Zemi\RegionManager\Model\States;

/**
 * Class Save
 * @package Zemi\RegionManager\Controller\Adminhtml\States
 */
class Save extends Action
{
    /**
     * @var Session
     */
    protected $_modelSession;
    /**
     * @var States
     */
    protected $_model;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param Session $session
     * @param States $model
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        States $model
    )
    {
        $this->_modelSession = $session;
        $this->_model = $model;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_model;

            $id = $this->getRequest()->getParam('entity_id');
            if ($id) {
                $model->load($id);
            } else {
                unset($data['entity_id']);
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'zemi_regionmanader_states_prepare_save',
                ['data' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->messageManager->addSuccess(__('Saved States'));
                $this->_modelSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Errors please try again.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}