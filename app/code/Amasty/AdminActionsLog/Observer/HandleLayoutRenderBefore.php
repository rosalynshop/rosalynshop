<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Amasty\AdminActionsLog\Model\VisitHistory;
use Amasty\AdminActionsLog\Model\VisitHistoryDetails;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View;

class HandleLayoutRenderBefore implements ObserverInterface
{
    protected $objectManager;
    protected $context;

    public function __construct(
        View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
        $this->context = $context;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $context = $this->context;
        $session = $context->getSession();
        $sessionId = $session->getSessionId();
        $visitEntityData = $this->objectManager->get(VisitHistory::class)->getVisitEntity($sessionId)->getData();
        if (!empty($visitEntityData)) {
            $pageTitleBlock = $context->getLayout()->getBlock('page.title');
            if ($pageTitleBlock !== false) {
                $pageConfig = $context->getPageConfig();
                $title = $pageConfig->getTitle()->get();
                $pageUrl = $context->getUrlBuilder()->getCurrentUrl();

                $detailData['page_name'] = $title;
                $detailData['page_url'] = $pageUrl;
                $detailData['session_id'] = $sessionId;
                $detailData['visit_id'] = $visitEntityData['id'];

                /**
                 * @var VisitHistoryDetails $detailsModel
                 */
                $detailsModel = $this->objectManager->get(VisitHistoryDetails::class);
                $detailsModel->saveLastPageDuration($sessionId);
                $session->setLastPageTime(time());
                $detailsModel->setData($detailData);
                $detailsModel->save();
            }
        }
    }
}
