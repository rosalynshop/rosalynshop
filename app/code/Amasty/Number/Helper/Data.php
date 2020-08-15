<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Number
 */


namespace Amasty\Number\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const HOUR_IN_SECONDS = 3600;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|null
     */
    protected $_scopeConfig = null;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\Collection
     */
    protected $configCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $configValueFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Amasty\Number\Model\CollectionProvider
     */
    protected $collectionProvider;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $state;

    /**
     * @var
     */
    protected $_cacheEnabled;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Number\Model\CollectionProvider $collectionProvider,
        \Magento\Framework\App\Cache\StateInterface $state,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\ResourceConnection $connection,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->configCollectionFactory = $configCollectionFactory;
        $this->configValueFactory = $configValueFactory;
        $this->storeManager = $storeManager;
        $this->objectManager = $objectManager;
        $this->collectionProvider = $collectionProvider;
        $this->state = $state;
        $this->cacheTypeList = $cacheTypeList;
        $this->connection = $connection;
        $this->order = $order;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param $path
     * @param null $storeId
     * @param string $scope
     *
     * @return mixed
     */
    public function getConfigValueByPath(
        $path,
        $storeId = null,
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE
    ) {
        return $this->_scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * Gets not cached config row as object.
     *
     * @param string $path
     * @param int $storeId
     * @param string $type
     *
     * @return \Magento\Framework\App\Config\ValueInterface|\Magento\Framework\DataObject
     */
    public function getNotCachedConfig($path, $storeId, $type)
    {
        $cfg = $this->getConfigValueByPath('amnumber/' . $type, $storeId);

        $scope = 'default';
        $scopeId = 0;
        if ($cfg['per_store']) {
            $scope = 'stores';
            $scopeId = $storeId;
        } elseif ($cfg['per_website']) {
            $scope = 'websites';
            $scopeId = $this->storeManager->getStore($storeId)->getWebsiteId();
        }

        $collection = $this->configCollectionFactory->create();

        $collection->addFieldToFilter('scope', $scope);
        $collection->addFieldToFilter('scope_id', $scopeId);
        $collection->addFieldToFilter('path', 'amnumber/' . $type . '/' . $path);
        $collection->setPageSize(1);

        if ($collection->getSize()) {
            $value = $collection->getFirstItem();
        } else {
            $value = $this->configValueFactory->create();
            $value->setScope($scope);
            $value->setScopeId($scopeId);
            $value->setPath('amnumber/' . $type . '/' . $path);
        }

        return $value;
    }

    /**
     * @param $type
     * @param $storeId
     * @param string $orderId
     * @param int $counter
     * @param string $pattern
     * @param bool $isSame
     *
     * @return mixed
     */
    public function getFormatIncrementId($type, $storeId, $orderId = '', $counter = 0, $pattern = '', $isSame = false)
    {
        try {
            $order = $this->order->loadByIncrementId($orderId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $order = false;
        }

        if (!$order || !$order->getId()) {
            $order = $this->checkoutSession->getQuote();
        }

        $timeOffset = trim($this->getConfigValueByPath('amnumber/general/offset', $storeId));
        if (!preg_match('/^[+\-]\d+$/', $timeOffset)) {
            $timeOffset = 0;
        }
        $now = self::HOUR_IN_SECONDS * $timeOffset + time();

        $cfg = $this->getConfigValueByPath('amnumber/' . $type, $storeId);
        $start = max((int)($cfg['start']), 0);
        /** @var \Magento\Framework\App\Config\Value $counterDateConfig */
        $counterDateConfig = $this->getNotCachedConfig('date', $storeId, $type);
        /** @var \Magento\Framework\App\Config\Value $counterConfig */
        $counterConfig = $this->getNotCachedConfig('counter', $storeId, $type);

        if ($counterConfig->getValue() > 0) {
            if ($cfg['reset']) {
                if (!$counterDateConfig->getValue()
                    || date($cfg['reset'], $now) != date($cfg['reset'], strtotime($counterDateConfig->getValue()))
                ) {
                    $counterConfig->setValue($start);
                }
            }
        }

        $counterDateConfig->setValue(date('Y-m-d', $now));
        if (!$isSame) {
            $counterDateConfig->save();
        }

        if ($counter == 0) {
            $increment = max((int)($cfg['increment']), 1);
            $counter = (int)($counterConfig->getValue()) + $increment;
        }

        if ($counter < $cfg['start']) {
            $counter = $cfg['start'];
        }

        $counterConfig->setValue($counter);
        if (!$isSame) {
            $counterConfig->save();
        }

        if ((int)($cfg['pad']) && $counter !== '') {
            $counter = str_pad($counter, (int)($cfg['pad']), '0', STR_PAD_LEFT);
        }

        $countryCode = '';
        if ($order && $order->getId()) {
            if ($order->getShippingAddress()) {
                $countryCode = $order->getShippingAddress()
                    ->getCountryId();
            } elseif ($order->getBillingAddress()) {
                $countryCode = $order->getBillingAddress()
                    ->getCountryId();
            }
        }

        $vars = [
            'store_id'     => $storeId,
            'store'        => $storeId,
            'yy'           => date('y', $now),
            'yyyy'         => date('Y', $now),
            'mm'           => date('m', $now),
            'm'            => date('n', $now),
            'dd'           => date('d', $now),
            'd'            => date('j', $now),
            'hh'           => date('H', $now),
            'rand'         => rand(1000, 9999),
            'counter'      => $counter,
            'order_id'     => $orderId,
            'country_code' => $countryCode
        ];

        if (!$isSame) {
            $incrementId = $this->getNumberByPattern((!$pattern ? $cfg['format'] : $pattern), $vars);
        } else {
            return $orderId;
        }

        if ($this->isIncrementIdExist($incrementId, $type)) {
            $increment = max((int)($cfg['increment']), 1);
            $counter = (int)($counter) + $increment;

            return $this->getFormatIncrementId($type, $storeId, $orderId, $counter, 0, $isSame);
        } else {

            return $incrementId;
        }
    }

    /**
     * @param $pattern
     * @param $vars
     *
     * @return mixed|string
     */
    protected function getNumberByPattern($pattern, $vars)
    {
        foreach ($vars as $k => $v) {

            if ($k == 'country_code' && !$v) {
                continue;
            }

            $pattern = str_replace('{' . $k . '}', $v, $pattern);
        }

        return $pattern;
    }

    public function flushConfigCache()
    {
        $cacheType = \Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER;

        if ($this->isCacheEnabled($cacheType)) {
            $this->cacheTypeList->cleanType($cacheType);
        }
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    public function isCacheEnabled($type)
    {
        if (!isset($this->_cacheEnabled)) {
            $this->_cacheEnabled = $this->state->isEnabled($type);
        }

        return $this->_cacheEnabled;
    }

    /**
     * @param string $incrementId
     *
     * @return bool
     */
    public function isIncrementIdExist($incrementId, $type)
    {
        $collection = $this->getCollectionByType($type);
        $collection->addFieldToFilter('increment_id', $incrementId)->setPageSize(1);

        return $collection->getSize() ? true : false;
    }

    /**
     * @param $type
     *
     * @return null|object
     */
    public function getCollectionByType($type)
    {
        return $this->collectionProvider->getCollection($type);
    }

    /**
     * @param $type
     *
     * @return string
     */
    public function getLastIncrementId($type)
    {
        $collection = $this->getCollectionByType($type);
        $collection->setOrder('entity_id', \Magento\Framework\Data\Collection::SORT_ORDER_DESC)->setPageSize(1);

        return $collection->getSize() ? $collection->getLastItem()->getIncrementId() : '';
    }
}
