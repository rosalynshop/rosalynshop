<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Controller\Adminhtml\Wards;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Zemi\RegionManager\Model\Wards;
use RuntimeException;

/**
 * Class Save
 * @package Zemi\RegionManager\Controller\Adminhtml\Wards
 */
class Save extends Action
{
    /**
     * @var Session
     */
    protected $_modelSession;
    /**
     * @var Wards
     */
    protected $_model;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param Session $session
     * @param Wards $model
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        Wards $model
    ) {
        $this->_modelSession = $session;
        $this->_model = $model;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
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
                'zemi_regionmanager_wards_prepare_save',
                ['data' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->messageManager->addSuccess(__('Saved Wards!.'));
                $this->_modelSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving a record.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}