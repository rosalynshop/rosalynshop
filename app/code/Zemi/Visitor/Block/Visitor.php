<?php

namespace Zemi\Visitor\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Visitor
 * @package Zemi\Visitor\Block
 */
class Visitor extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Visitor
     */
    protected $_visitor;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $_localeDate;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Event\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Visitor constructor.
     * @param Template\Context $context
     * @param \Magento\Customer\Model\Visitor $visitor
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Reports\Model\ResourceModel\Event\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\Visitor $visitor,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Reports\Model\ResourceModel\Event\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_visitor = $visitor;
        $this->_localeDate = $localeDate;
        $this->collectionFactory = $collectionFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getVisitors()
    {
        $this->getViewedProductsForCurrentCustomer();
        return $this->_visitor->getCollection()->addFieldToFilter('last_visit_at', [
            'from' => $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s')
        ]);

    }

    /**
     * @return bool|\Magento\Reports\Model\ResourceModel\Event\Collection
     */
    public function getViewedProductsForCurrentCustomer()
    {
        $viewed = [];
        if ($this->customerSession->isLoggedIn()) {
            /** @var \Magento\Reports\Model\ResourceModel\Event\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('subject_id', $this->customerSession->getCustomerId());
            $collection->distinct(true);

//            var_dump($collection->getData());die;

            $viewed = $collection;
        }
        $collection = $this->collectionFactory->create();
//        $collection->addFieldToFilter('subject_id', $this->customerSession->getCustomerId());
//        $collection->distinct(true);

        $collection->getSelect()->limit(3);
//        var_dump($viewed->getData());die;
        return $viewed;
    }
}
