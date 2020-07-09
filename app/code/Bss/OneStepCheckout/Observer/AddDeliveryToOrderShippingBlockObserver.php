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
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use Bss\OneStepCheckout\Helper\Data;
use Bss\OneStepCheckout\Helper\Config;

/**
 * Class AddDeliveryToOrderShippingBlockObserver
 *
 * @package Bss\OneStepCheckout\Observer
 */
class AddDeliveryToOrderShippingBlockObserver implements ObserverInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var Data
     */
    private $oscHelper;

    /**
     * One step checkout config helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @param LayoutInterface $layout
     * @param Data $oscHelper
     * @param Config $configHelper
     */
    public function __construct(
        LayoutInterface $layout,
        Data $oscHelper,
        Config $configHelper
    ) {
        $this->layout = $layout;
        $this->oscHelper = $oscHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * Execute observer
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($observer->getElementName() == 'sales.order.info' && $this->configHelper->isEnabled()) {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
            $deliveryBlock = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
            $date = $this->oscHelper->formatDateTime($order);
            $deliveryBlock->setDeliveryDate($date)
                ->setDeliveryComment($order->getDeliveryComment())
                ->setActiveJs(false)
                ->setTemplate('Bss_OneStepCheckout::delivery.phtml');
            $html = $observer->getTransport()->getOutput() . $deliveryBlock->toHtml();
            $observer->getTransport()->setOutput($html);
        }
    }
}
