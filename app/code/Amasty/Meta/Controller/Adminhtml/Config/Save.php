<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml\Config;

class Save extends \Amasty\Meta\Controller\Adminhtml\Config
{

    public function execute()
    {
        $model = $this->_objectManager->create('Amasty\Meta\Model\\'.$this->_modelName);
        $data  = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
                if ($id != $model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                }
            }

            $model->addData($data);

            try {
                $this->prepareForSave($model);
                $model->save();
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                $msg = __('%1 has been successfully saved', $this->_title);
                $this->messageManager->addSuccess($msg);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                } else {
                    $this->_redirect('*/*');
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $id]);
            }
            return;
        }

        $this->messageManager->addError(__('Unable to find a record to save'));
        $this->_redirect('*/*');
    }

    protected function prepareForSave($model)
    {
        return true;
    }
}