<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\Sales\Model\Service;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Helper\Config;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;
use Amasty\AdvancedReview\Model\ReminderFactory;

/**
 * Class OrderService
 * @package Amasty\AdvancedReview\Plugin\Sales\Model\Service
 */
class OrderService
{
    /**
     * @var ReminderFactory
     */
    private $reminderFactory;

    /**
     * @var \Amasty\AdvancedReview\Model\Repository\ReminderRepository
     */
    private $reminderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * (should be protected - used in child)
     * @var Config
     */
    protected $config;

    public function __construct(
        ReminderFactory $reminderFactory,
        \Amasty\AdvancedReview\Model\Repository\ReminderRepository $reminderRepository,
        \Amasty\AdvancedReview\Helper\Config $config,
        LoggerInterface $logger
    ) {
        $this->reminderFactory = $reminderFactory;
        $this->reminderRepository = $reminderRepository;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param $subject
     * @param OrderInterface $order
     */
    public function afterPlace($subject, OrderInterface $order)
    {
        if ($this->config->isReminderEnabled()
            && !$this->config->getTriggerOrderStatus()
        ) {
            $this->saveOrderToReminder($order);
        }

        return $order;
    }

    /**
     * should be protected
     * @param OrderInterface $order
     */
    protected function saveOrderToReminder(OrderInterface $order)
    {
        try {
            /** @var ReminderInterface $model */
            $model = $this->reminderFactory->create();
            if ($this->validate($order->getEntityId())) {
                return;
            }
            $model->setOrderId($order->getEntityId());

            $days = $this->config->getDaysToSend();
            $model->setSendDate(strtotime("+ " . $days . " day"));

            $this->reminderRepository->save($model);
        } catch (\Exception $ex) {
            $this->logger->critical($ex);
        }
    }

    /**
     * @param $orderId
     *
     * @return bool
     */
    protected function validate($orderId)
    {
        $model = $this->reminderFactory->create()->load($orderId, 'order_id');
        return (bool)$model->getEntityId();
    }
}
