<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_OneStepCheckout
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Model;

use Magento\Sales\Model\Order\Status\HistoryFactory;
use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class AdditionalData
 *
 * @package Bss\OneStepCheckout\Model
 */
class AdditionalData
{
    /**
     * Order history factory
     *
     * @var HistoryFactory $historyFactory
     */
    private $historyFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Subcriber
     *
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriber;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Initialize dependencies.
     *
     * @param HistoryFactory $historyFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param Subscriber $subscriber
     * @param LoggerInterface $logger
     */
    public function __construct(
        HistoryFactory $historyFactory,
        OrderRepositoryInterface $orderRepository,
        Subscriber $subscriber,
        LoggerInterface $logger
    ) {
        $this->historyFactory = $historyFactory;
        $this->orderRepository = $orderRepository;
        $this->subscriber = $subscriber;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $additionalData
     * @return void
     */
    public function saveDelivery($quote, $additionalData)
    {
        if (isset($additionalData['delivery_date'])) {
            $quote->setDeliveryDate($additionalData['delivery_date']);
        }
        if (isset($additionalData['delivery_comment'])) {
            $quote->setDeliveryComment($additionalData['delivery_comment']);
        }
    }

    /**
     * @param int $orderId
     * @param array $additionalData
     * @return void
     */
    public function saveComment($orderId, $additionalData)
    {
        $order = $this->orderRepository->get($orderId);
        try {
            if (isset($additionalData['order_comment'])) {
                $comment = $order->getCustomerName();
                $comment .= ': ';
                $comment .= $additionalData['order_comment'];
                if ($order->getId()) {
                    $status = $order->getStatus();
                    $history = $this->historyFactory->create();
                    $history->setComment($comment)
                        ->setParentId($orderId)
                        ->setIsVisibleOnFront(1)
                        ->setIsCustomerNotified(0)
                        ->setEntityName('order')
                        ->setStatus($status)
                        ->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param int $orderId
     * @param array $additionalData
     * @return void
     */
    public function subscriber($orderId, $additionalData)
    {
        $order = $this->orderRepository->get($orderId);
        try {
            if (isset($additionalData['subscribe']) && $additionalData['subscribe']) {
                if ($order->getCustomerId()) {
                    $subscriberModel = $this->subscriber->loadByCustomerId($order->getCustomerId());
                    if (!$subscriberModel->isSubscribed()) {
                        $this->subscriber->subscribeCustomerById($order->getCustomerId());
                    }
                } else {
                    $subscriberModel = $this->subscriber->loadByEmail($order->getCustomerEmail());
                    if (!$subscriberModel->isSubscribed()) {
                        $this->subscriber->subscribe($order->getCustomerEmail());
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
