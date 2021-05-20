<?php

namespace Zemi\Visitor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Zemi\Visitor\Model\Visitor;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

/**
 * Reports Event observer model
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class UserProductView implements ObserverInterface
{
    /**
     * @var Visitor
     */
    protected $_zemiVisitor;

    /**
     * @var \Zemi\Visitor\Helper\Data
     */
    protected $_helperData;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * UserProductView constructor.
     * @param \Zemi\Visitor\Helper\Data $helperData
     * @param Visitor $zemiVisitor
     * @param \Magento\Customer\Model\Session $customerSession
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(
        \Zemi\Visitor\Helper\Data $helperData,
        \Zemi\Visitor\Model\Visitor $zemiVisitor,
        \Magento\Customer\Model\Session $customerSession,
        RemoteAddress $remoteAddress
    ) {
        $this->_helperData = $helperData;
        $this->_zemiVisitor = $zemiVisitor;
        $this->customerSession = $customerSession;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $productId = $observer->getEvent()->getProduct()->getId();
        $exitProductId = $this->_helperData->getVisitorProductId($productId);
        $dateTime = date('Y-m-d H:i:s', strtotime('+7 hour', strtotime(gmdate('Y-m-d H:i:s'))));
        if ($this->customerSession->isLoggedIn()) {
            if (empty($exitProductId) && $exitProductId != $productId) {
                $viewed = [
                    'date_time'      => $dateTime,
                    'ip_address'     => $this->remoteAddress->getRemoteAddress(),
                    'product_id'     => $productId,
                    'customer_id'    => $this->customerSession->getCustomer()->getId(),
                ];
                $this->_zemiVisitor->setData($viewed)->save();
            }
        }
        return $this;
    }
}
