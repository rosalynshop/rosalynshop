<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Controller\Adminhtml\Link;

/**
 * Class Index
 * @package Amasty\CrossLinks\Controller\Adminhtml\Link
 */
class Index extends \Amasty\CrossLinks\Controller\Adminhtml\Link
{
    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_CrossLinks::seo')
            ->addBreadcrumb(__('Cross Link Management'), __('Cross Link Management'));
        $resultPage->getConfig()->getTitle()->prepend(__('Cross Link Management'));
        return $resultPage;
    }
}
