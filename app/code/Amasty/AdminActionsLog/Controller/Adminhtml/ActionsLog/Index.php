<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\ActionsLog;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Class Index
 */
class Index extends Action
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_AdminActionsLog::amaudit');
        $resultPage->addBreadcrumb(__('Admin Actions Log'), __('Actions Log'));
        $resultPage->getConfig()->getTitle()->prepend(__('Actions Log'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::actions_log');
    }
}
