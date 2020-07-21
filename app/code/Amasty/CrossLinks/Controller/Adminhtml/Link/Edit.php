<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Controller\Adminhtml\Link;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Edit
 * @package Amasty\CrossLinks\Controller\Adminhtml\Link
 */
class Edit extends \Amasty\CrossLinks\Controller\Adminhtml\Link
{
    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        if ($id = $this->getRequest()->getParam('link_id')) {
            try{
                $link = $this->linkRepository->get($id);
            } catch(NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This link no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $link = $this->linkFactory->create();
        }
        $data = $this->sessionFactory->create()->getFormData(true);
        if (!empty($data)) {
            $link->setData($data);
        }

        $this->coreRegistry->register('current_link', $link);

        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Amasty_CrossLinks::seo')
            ->addBreadcrumb(__('Cross Link Management'), __('Cross Link Management'))
            ->addBreadcrumb(
                $id ? __('Edit Link') : __('New Link'),
                $id ? __('Edit Link') : __('New Link')
            );
        $resultPage->getConfig()->getTitle()->prepend(__('Cross Link Management'));
        $resultPage->getConfig()->getTitle()->prepend($link->getId() ? $link->getTitle() : __('New Link'));

        return $resultPage;
    }
}
