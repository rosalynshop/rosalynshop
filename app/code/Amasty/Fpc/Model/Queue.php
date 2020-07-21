<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Amasty\Fpc\Api\QueuePageRepositoryInterface;
use Amasty\Fpc\Exception\LockException;
use Amasty\Fpc\Helper\Http as HttpHelper;
use Amasty\Fpc\Model\Queue\Page;
use Magento\Customer\Model\Group;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Amasty\Fpc\Model\Config\Source\QuerySource;
use Magento\Framework\Flag\FlagResource;
use Magento\Framework\FlagFactory;

class Queue
{
    const DEFAULT_VALUE = null;

    const PROCESSING_FLAG = 'amasty_fpc_warmer_processing';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Log
     */
    private $crawlerLog;

    /**
     * @var ResourceModel\Queue\Page\CollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var \Amasty\Fpc\Model\QueuePageRepository
     */
    private $pageRepository;

    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Source\Factory
     */
    private $sourceFactory;

    /**
     * @var FlagFactory
     */
    private $flagFactory;

    /**
     * @var FlagResource
     */
    private $flagResource;

    public function __construct(
        Config $config,
        Log $crawlerLog,
        ResourceModel\Queue\Page\CollectionFactory $pageCollectionFactory,
        QueuePageRepositoryInterface $pageRepository,
        Crawler $crawler,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        Source\Factory $sourceFactory,
        FlagFactory $flagFactory,
        FlagResource $flagResource
    ) {
        $this->config = $config;
        $this->crawlerLog = $crawlerLog;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->pageRepository = $pageRepository;
        $this->crawler = $crawler;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->sourceFactory = $sourceFactory;
        $this->flagFactory = $flagFactory;
        $this->flagResource = $flagResource;
    }

    protected function lock()
    {
        if ($this->getFlag(self::PROCESSING_FLAG)) {
            throw new LockException(__('Another lock detected (the Warmer queue is in a progress).'));
        }

        $this->saveFlag(self::PROCESSING_FLAG, true);
    }

    protected function unlock()
    {
        $this->saveFlag(self::PROCESSING_FLAG, false);
    }

    /**
     * Generate queue to crawl
     *
     * @return array
     * @throws LockException
     */
    public function generate()
    {
        $this->lock();
        $processedItems = 0;
        $queueLimit = $this->config->getQueueLimit();
        $source = $this->getSource($queueLimit);

        if (empty($source)) {
            $this->unlock();

            return [false, $processedItems];
        }

        try {
            $this->pageRepository->clear();
        } catch (\Exception $e) {
            $this->unlock();

            return [false, $processedItems];
        }

        if (count($source) > $queueLimit) {
            $source = array_slice($source, 0, $queueLimit);
        }

        foreach ($source as $item) {
            $this->pageRepository->addPage($item);
            $processedItems++;

            if (!$this->getFlag(self::PROCESSING_FLAG)) {
                return [false, $processedItems];
            }
        }

        $this->unlock();

        return [true, $processedItems];
    }

    public function forceUnlock()
    {
        $this->saveFlag(self::PROCESSING_FLAG, false);
    }

    protected function getCombinations()
    {
        $stores = $this->config->getStores();
        $currencies = $this->config->getCurrencies();
        $customerGroups = $this->config->getCustomerGroups();
        /** @var Store $defaultStore */
        $defaultStore = $this->storeManager->getWebsite()->getDefaultStore();
        $defaultCurrency = $defaultStore->getDefaultCurrency()->getCode();

        if (!in_array($defaultStore->getId(), $stores)) {
            $stores[] = $defaultStore->getId();
        }

        // Replace default values with empty
        $this
            ->replaceDefaultValue($customerGroups, Group::NOT_LOGGED_IN_ID)
            ->replaceDefaultValue($currencies, $defaultCurrency)
            ->replaceDefaultValue($stores, $defaultStore->getId());

        return [$stores, $currencies, $customerGroups];
    }

    protected function replaceDefaultValue(&$values, $default)
    {
        $key = array_search($default, $values);

        if (false !== $key) {
            unset($values[$key]);
            array_unshift($values, self::DEFAULT_VALUE);
        }

        // Add default value if nothing selected
        if (empty($values)) {
            array_unshift($values, self::DEFAULT_VALUE);
        }

        return $this;
    }

    public function process()
    {
        $this->lock();
        $uncachedPages = $this->getUncachedPages();
        $this->crawlerLog->trim();
        list($stores, $currencies, $customerGroups) = $this->getCombinations();
        $pagesCrawled = 0;

        if ($this->config->isMultipleCurl()) {
            $pagesCrawled = $this->processMultipleCurl(
                $uncachedPages,
                $stores,
                $currencies,
                $customerGroups
            );
        } else {
            /** @var Page $page */
            foreach ($uncachedPages as $page) {
                $pagesCrawled += $this->processCombinations(
                    $page,
                    $page->getStore() ? [$page->getStore()] : $stores,
                    $currencies,
                    $customerGroups
                );
            }
        }

        $this->unlock();

        return $pagesCrawled;
    }

    protected function getUncachedPages()
    {
        $uncachedPages = [];
        $pageCounter = 0;
        /** @var ResourceModel\Queue\Page\Collection $pages */
        $pages = $this->pageCollectionFactory->create()->setOrder('rate');
        $batchSize = $this->config->getBatchSize();

        foreach ($pages as $page) {
            if ($this->crawler->isAlreadyCached($page->getUrl())) {
                $this->pageRepository->delete($page);
                continue;
            }

            if ($pageCounter >= $batchSize) {
                break;
            }

            $uncachedPages[] = $page;
            $pageCounter++;
        }

        return $uncachedPages;
    }

    /**
     * @param $pages
     * @param $stores
     * @param $currencies
     * @param $customerGroups
     *
     * @return int $pagesCrawled
     */
    public function processMultipleCurl(
        $pages,
        $stores,
        $currencies,
        $customerGroups
    ) {
        $pagesCrawled = 0;
        $delay = $this->config->getDelay();
        $mobiles = $this->config->isProcessMobile() ? [true, false] : [false];
        $originPages = $pages;

        foreach ($customerGroups as $customerGroup) {
            foreach ($stores as $store) {
                foreach ($currencies as $currency) {
                    foreach ($mobiles as $mobile) {
                        $pages = $originPages;

                        while ($pages) {
                            $this->crawler->initMultipleCurl($pages, $mobile);
                            $pagesCrawled +=
                                $this->crawler->runMultipleCurl($customerGroup, $store, $currency, $mobile);
                            usleep($delay * 1000);
                        }
                    }
                }
            }
        }

        return $pagesCrawled;
    }

    /**
     * Process all page combinations and return count of actually crawled pages
     *
     * @param $page
     * @param $stores
     * @param $currencies
     * @param $customerGroups
     *
     * @return int
     */
    public function processCombinations(
        $page,
        $stores,
        $currencies,
        $customerGroups
    ) {
        $pagesCrawled = 0;
        $delay = $this->config->getDelay();
        $mobiles = $this->config->isProcessMobile() ? [true, false] : [false];

        foreach ($customerGroups as $customerGroup) {
            foreach ($stores as $store) {
                foreach ($currencies as $currency) {
                    foreach ($mobiles as $mobile) {
                        $status = $this->crawler->processPage($page, $customerGroup, $store, $currency, $mobile);
                        usleep($delay * 1000);

                        if ($status != HttpHelper::STATUS_ALREADY_CACHED) {
                            $pagesCrawled++;
                        }
                    }
                }
            }
        }

        return $pagesCrawled;
    }

    /**
     * Return pages to crawl from selected source
     *
     * @param int $queueLimit
     *
     * @return array
     */
    public function getSource($queueLimit)
    {
        $type = $this->config->getSourceType();

        if (QuerySource::SOURCE_COMBINE == $type) {
            $eMessage = __('Combine is selected as source: ');

            try {
                $filePages = $this->sourceFactory->create(QuerySource::SOURCE_TEXT_FILE)
                    ->getPages($queueLimit, $eMessage);
            } catch (\Exception $e) {
                $this->logger->warning($eMessage . $e->getMessage());
                $filePages = [];
            }

            $queueLimit = $queueLimit - count($filePages);

            try {
                $sitemapPages = $this->sourceFactory->create(QuerySource::SOURCE_SITE_MAP)
                    ->getPages($queueLimit, $eMessage);
            } catch (\Exception $e) {
                $this->logger->warning($eMessage . $e->getMessage());
                $sitemapPages = [];
            }

            $source = array_merge($filePages, $sitemapPages);
        } else {
            $eMessage = '';
            switch ($type) {
                case QuerySource::SOURCE_TEXT_FILE:
                    $eMessage = __('File is selected as source: ');
                    break;
                case QuerySource::SOURCE_SITE_MAP:
                    $eMessage = __('Sitemap XML is selected as source: ');
                    break;
                case QuerySource::SOURCE_ACTIVITY:
                    $eMessage = __('Activity is selected as source: ');
                    break;
            }

            try {
                $source = $this->sourceFactory->create($type)->getPages($queueLimit, $eMessage);
            } catch (\Exception $e) {
                $this->logger->warning($eMessage . $e->getMessage());

                return [];
            }
        }

        if (empty($source)) {
            $this->logger->warning($eMessage . __('but source is empty'));

            return [];
        }

        foreach ($source as $k => &$item) {
            if ($this->isIgnored($item['url'])) {
                unset($source[$k]);
            }
        }

        usort($source, function ($a, $b) {
            if ($a['rate'] < $b['rate']) {
                return 1;
            } elseif ($a['rate'] > $b['rate']) {
                return -1;
            } else {
                return 0;
            }
        });

        return $source;
    }

    public function isIgnored($path)
    {
        $ignoreList = $this->config->getExcludePages();

        foreach ($ignoreList as $pattern) {
            if (preg_match("|{$pattern['expression']}|", $path)) {
                return true;
            }
        }

        return false;
    }

    //we don't use FlagManager due to magento 2.1.x compatibility
    public function getFlag($code)
    {
        return $this->createFlag($code)->getFlagData();
    }

    private function saveFlag($code, $value)
    {
        $flag = $this->createFlag($code);
        $flag->setFlagData($value);
        $this->flagResource->save($flag);
    }

    private function createFlag($code)
    {
        $flag = $this->flagFactory->create(['data' => ['flag_code' => $code]]);
        $this->flagResource->load(
            $flag,
            $code,
            'flag_code'
        );

        return $flag;
    }
}
