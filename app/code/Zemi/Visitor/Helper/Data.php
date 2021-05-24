<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\Visitor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Zemi\Visitor\Helper
 */
class Data extends AbstractHelper
{
    const XML_ZEMI_VISITOR_ENABLE = 'zemi_visitor/general/enable';
    const XML_ZEMI_EMAIL_SENT = 'zemi_visitor/general/email_sent';

    /**
     * @var \Magento\Customer\Model\Visitor
     */
    protected $_visitor;

    /**
     * @var \Zemi\Visitor\Model\Visitor
     */
    protected $_zemiVisitor;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $_localeDate;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Visitor $visitor
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Visitor $visitor,
        \Zemi\Visitor\Model\Visitor $zemiVisitor,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->_visitor = $visitor;
        $this->_zemiVisitor = $zemiVisitor;
        $this->_localeDate = $localeDate;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getVisitors()
    {
        return $this->_visitor->getCollection()->addFieldToFilter('last_visit_at', [
            'from' => $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * @param $productId
     * @return array|null
     */
    public function getVisitorProductId($productId)
    {
        $data = [];
        foreach ($this->getVisitorCollection($productId) as $item) {
            $data = $item['product_id'];
        }
        return $data;
    }

    /**
     * @param $productId
     * @return array|null
     */
    public function getVisitorCustomerData()
    {
        $collection = $this->_zemiVisitor->getCollection()->addFieldToFilter('date_time', [
            'from' => $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s')
        ]);
        return $collection->getData();
    }

    /**
     * @param $productId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getVisitorCollection($productId)
    {
        return $this->_zemiVisitor->getCollection()->addFieldToFilter(
            'product_id', $productId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function visitorEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_VISITOR_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function visitorEmailAdmin($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_ZEMI_EMAIL_SENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ? : "denda.hanoi@gmail.com";
    }
}
