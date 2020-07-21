<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Controller\Adminhtml\Link;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Delete
 * @package Amasty\CrossLinks\Controller\Adminhtml\Link
 */
class Delete extends \Amasty\CrossLinks\Controller\Adminhtml\Link
{
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('link_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $this->linkRepository->delete($this->linkRepository->get($id));
                $this->messageManager->addSuccessMessage(__('Link has been deleted successfully'));
                return $resultRedirect->setPath('*/*/');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This link no longer exists.'));
                return $resultRedirect->setPath('*/*/edit', ['group_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['group_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('Requested link does not exist'));

        return $resultRedirect->setPath('*/*/');
    }
}
