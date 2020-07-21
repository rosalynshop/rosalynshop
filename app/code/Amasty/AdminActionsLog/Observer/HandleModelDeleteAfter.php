<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Amasty\AdminActionsLog\Model\Log;
use Magento\Framework\Event\ObserverInterface;

class HandleModelDeleteAfter implements ObserverInterface
{
    protected $objectManager;
    protected $helper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\AdminActionsLog\Helper\Data $helper
    ) {
        $this->objectManager = $objectManager;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->needToSave($observer->getObject())) {
            $object = $observer->getObject();
            /** @var Log $logModel */
            $logModel = $this->objectManager->get(Log::class);
            $data = $logModel->prepareLogData($object);
            if (!isset($data['username'])) {
                return;
            }
            $logModel->setData($data);
            $logModel->save();
        }
    }
}
