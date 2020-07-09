<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bss\OneStepCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bss\OneStepCheckout\Helper\Config;

class SalesEventQuoteSubmitBeforeObserver implements ObserverInterface
{
    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        Config $configHelper
    ) {
        $this->configHelper = $configHelper;
    }
    /**
     * Set gift messages to order from quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if ($this->configHelper->isEnabled()) {
            $quote = $observer->getEvent()->getQuote();
            $order = $observer->getEvent()->getOrder();
            $order->setDeliveryDate($quote->getDeliveryDate());
            $order->setDeliveryComment($quote->getDeliveryComment());
        }
        return $this;
    }
}
