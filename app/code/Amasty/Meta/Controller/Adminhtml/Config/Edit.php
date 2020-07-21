<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml\Config;

class Edit extends \Amasty\Meta\Controller\Adminhtml\Config
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');

        $model = $this->_objectManager->create('Amasty\Meta\Model\\'.$this->_modelName)->load($id);
        if ($id && ! $model->getId()) {
            $this->messageManager->addError(__('This item no longer exists.'));
            $this->_redirect('*/*');
            return;
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        } else {
            $this->_prepareForEdit($model);
        }

        $this->_coreRegistry->register('ammeta_config', $model);
        $this->_view->loadLayout();
        $this->_setActiveMenu('cms/amseotoolkit/ammeta');

        $this->_addContent(
            $this->_view->getLayout()->createBlock('Amasty\Meta\Block\Adminhtml\\' . $this->_blockName . '\Edit')
        );

        if($model->getId()) {
            $title = __('Edit Template #`%1`', $model->getId());
        } else {
            $title = __("Add New");
        }

        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_view->renderLayout();
    }

    public function _prepareForEdit($model)
    {
        return true;
    }
}
