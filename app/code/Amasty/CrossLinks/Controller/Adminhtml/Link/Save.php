<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Controller\Adminhtml\Link;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Save
 * @package Amasty\CrossLinks\Controller\Adminhtml\Link
 */
class Save extends \Amasty\CrossLinks\Controller\Adminhtml\Link
{
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if ($id = $this->getRequest()->getParam('link_id')) {
                try{
                    $link = $this->linkRepository->get($id);
                } catch (NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage(__('This link no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $link = $this->linkFactory->create();
            }
            $link->setData($data);
            try {
                $this->linkRepository->save($link);
                $this->messageManager->addSuccessMessage(__('Link has been saved successfully.'));
                $this->sessionFactory->create()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['link_id' => $link->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->sessionFactory->create()->setFormData($data);
                return $resultRedirect->setPath('*/*/edit',
                    ['link_id' => $this->getRequest()->getParam('link_id')]
                );
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
