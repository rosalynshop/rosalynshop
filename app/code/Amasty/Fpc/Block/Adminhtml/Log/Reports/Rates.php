<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Log\Reports;

use Magento\Backend\Block\Template;

/**
 * Class Rates
 *
 * @package Amasty\Fpc\Block\Adminhtml\Log\Reports
 */
class Rates extends Template
{
    const LOG_PAGES = 'log_pages';

    protected $_template = 'Amasty_Fpc::log/rates.phtml';

    /**
     * @var \Amasty\Fpc\Model\ResourceModel\Page\CollectionFactory
     */
    private $queueCollectionFactory;

    /**
     * @var \Amasty\Fpc\Model\Config
     */
    private $fpcConfig;

    /**
     * @var \Amasty\Fpc\Model\ResourceModel\Log\CollectionFactory
     */
    private $logCollectionFactory;

    /**
     * @var \Amasty\Fpc\Mpdel\ResourceModel\Reports\CollectionFactory
     */
    private $reportsCollectionFactory;

    public function __construct(
        Template\Context $context,
        \Amasty\Fpc\Model\Config $fpcConfig,
        \Amasty\Fpc\Model\ResourceModel\Queue\Page\CollectionFactory $queueCollectionFactory,
        \Amasty\Fpc\Model\ResourceModel\Log\CollectionFactory $logCollectionFactory,
        \Amasty\Fpc\Model\ResourceModel\Reports\CollectionFactory $reportsCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->fpcConfig = $fpcConfig;
        $this->logCollectionFactory = $logCollectionFactory;
        $this->reportsCollectionFactory = $reportsCollectionFactory;
    }

    /**
     * @return int
     */
    public function getHitsValue()
    {
        $hitsValue = $this->reportsCollectionFactory->create()
            ->getHitRate((int)$this->_scopeConfig->getValue('system/full_page_cache/ttl'));

        return $hitsValue;
    }

    /**
     * @return int
     */
    public function getCachedValue()
    {
        $warmBatch = $this->fpcConfig->getQueueLimit();
        $log = $this->getLogCount();

        $inCacheValue = $this->checkQueueNotEmpty() ? $log / $warmBatch * 100 : 0;

        return $inCacheValue;
    }

    /**
     * @return int
     */
    public function getPendingValue()
    {
        $pendingValue = 100 - $this->getCachedValue();

        return $pendingValue;
    }

    /**
     * @return string
     */
    public function getCacheType()
    {
        $cacheType = $this->_scopeConfig->getValue('system/full_page_cache/caching_application');

        switch ($cacheType) {
            case \Magento\PageCache\Model\Config::BUILT_IN:
                return __('Built-in');
                break;
            case \Magento\PageCache\Model\Config::VARNISH:
                return __('Varnish');
                break;
            default:
                return __('Unknown');
        }
    }

    /**
     * @return string
     */
    public function getCacheTtl()
    {
        $cacheTTL = (int)$this->_scopeConfig->getValue('system/full_page_cache/ttl')  / 3600;

        return $cacheTTL . 'h';
    }

    /**
     * @return bool
     */
    private function checkQueueNotEmpty()
    {
        return (bool)$this->queueCollectionFactory->create()->count();
    }

    /**
     * @return int
     */
    private function getLogCount()
    {
        if (!$this->getData(self::LOG_PAGES)) {
            $this->setData(self::LOG_PAGES, $this->logCollectionFactory->create()->count());
        }

        return $this->getData(self::LOG_PAGES);
    }
}
