<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Number
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Number\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderSaveBefore implements ObserverInterface
{
    protected $_salesObjects = ['invoice', 'shipment', 'creditmemo'];

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Amasty\Number\Helper\Data
     */
    protected $helper;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var bool
     */
    protected $isSetSame = false;

    /**
     * @var bool
     */
    protected $isSetPrefix = false;

    /**
     * @var bool
     */
    protected $isSetFormat = false;

    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * OrderSaveBefore constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Amasty\Number\Helper\Data                         $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\Number\Helper\Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return string
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $doc = null;
        $this->setType($observer);
        if (!$this->type) {
            return '';
        }

        if (in_array($this->type, $this->_salesObjects)) {
            $doc = $observer->getData($this->type);
        }

        if ($doc->getId()) {
            return '';
        }

        $order = $doc->getOrder();
        $storeId = $order->getStore()->getStoreId();
        $this->isSetSame = $this->scopeConfig->isSetFlag(
            'amnumber/' . $this->type . '/same',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $this->isSetPrefix = $this->scopeConfig->isSetFlag(
            'amnumber/' . $this->type . '/prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $this->isSetFormat = $this->scopeConfig->isSetFlag(
            'amnumber/' . $this->type . '/format',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ((!$this->isSetSame && !$this->isSetFormat)
            || !$this->helper->getConfigValueByPath('amnumber/general/enabled', $storeId)
        ) {
            return '';
        }

        $number = $this->_getFormatNumber($order->getIncrementId(), $storeId, $this->counter);

        if ($this->helper->isIncrementIdExist($number, $this->type)) {
            $this->counter++;
            $number = $this->execute($observer);
        }

        $this->helper->flushConfigCache();
        $doc->setIncrementId($number);

        return $number;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    protected function setType($observer)
    {
        foreach ($this->_salesObjects as $t) {
            if (is_object($observer->getData($t))) {
                $this->type = $t;
                break;
            }
        }
    }

    /**
     * @param string     $number
     * @param int|string $storeId
     * @param int        $counter
     *
     * @return string
     */
    protected function _getFormatNumber($number, $storeId, $counter = 0)
    {
        if ($number) {
            switch ($this->isSetSame) {
                case true:
                    $number = $this->getReplaceSameOrderNumber($number, $storeId, $counter);
                    break;
                case false:
                    $number = $this->getReplaceNotSameOrderNumber($number, $storeId);
                    break;
            }
        }

        return $number;
    }

    /**
     * @param string     $number
     * @param int|string $storeId
     * @param int        $counter
     *
     * @return string
     */
    protected function getReplaceSameOrderNumber($number, $storeId, $counter = 0)
    {
        $number = $this->helper->getFormatIncrementId('order', $storeId, $number, 0, '', $this->isSetSame);
        $prefix = $this->helper->getConfigValueByPath(
            'amnumber/' . $this->type . '/prefix',
            $storeId,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $replace = $this->helper->getConfigValueByPath(
            'amnumber/' . $this->type . '/replace',
            $storeId,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($replace) {
            $number = str_replace($replace, $prefix, $number);
        } else {
            $number = $prefix . $number;
        }

        if ($counter) {
            $number .= '-' . $counter;
        }

        return $number;
    }

    /**
     * @param string     $orderId
     * @param int|string $storeId
     *
     * @return string
     */
    protected function getReplaceNotSameOrderNumber($orderId, $storeId)
    {
        return $this->helper->getFormatIncrementId($this->type, $storeId, $orderId);
    }
}
