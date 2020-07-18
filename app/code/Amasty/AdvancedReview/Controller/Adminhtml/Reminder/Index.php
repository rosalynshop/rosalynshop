<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml\Reminder;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Amasty\AdvancedReview\Controller\Adminhtml\Reminder
 */
class Index extends \Amasty\AdvancedReview\Controller\Adminhtml\AbstractReminder
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_AdvancedReview::reminder');
        $resultPage->getConfig()->getTitle()->prepend(__('Review Reminder'));
        $resultPage->addBreadcrumb(__('Reminder'), __('Reminder'));

        return $resultPage;
    }
}
