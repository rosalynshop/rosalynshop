<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Model;

use Magento\Framework\App\Cache\StateInterface as CacheStateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Service\WarmerServiceInterface;
use Mirasvit\Core\Helper\Cron as CronHelper;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var CacheStateInterface
     */
    private $cacheState;

    /**
     * @var CronHelper
     */
    private $cronHelper;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        CacheStateInterface $cacheState,
        CronHelper $cronHelper,
        TimezoneInterface $timezone
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->filesystem  = $filesystem;
        $this->cacheState  = $cacheState;
        $this->cronHelper  = $cronHelper;
        $this->timezone    = $timezone;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue('cache_warmer/general/enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isPageCacheEnabled()
    {
        return $this->cacheState->isEnabled(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->timezone->date()->setTimezone(
            new \DateTimeZone('UTC')
        );
    }

    /**
     * @return bool
     */
    public function checkCronStatus()
    {
        return $this->cronHelper->checkCronStatus(null)[0];
    }

    /**
     * @return int
     */
    public function getWarmThreads()
    {
        $value = $this->scopeConfig->getValue('cache_warmer/performance/threads', ScopeInterface::SCOPE_STORE);

        $value = (int)$value;

        return $value ? $value : 1;
    }

    /**
     * Delay in microseconds
     * @return int
     */
    public function getWarmDelay()
    {
        return $this->scopeConfig->getValue('cache_warmer/performance/delay', ScopeInterface::SCOPE_STORE) * 1000;
    }

    /**
     * @return int
     */
    public function getJobRunThreshold()
    {
        $warmTime = $this->scopeConfig->getValue('cache_warmer/performance/job_time', ScopeInterface::SCOPE_STORE);

        if (!$warmTime) {
            $warmTime = 100;
        }

        return $warmTime;
    }

    /**
     * @param PageInterface $page
     * @return bool
     */
    public function isIgnoredPage(PageInterface $page)
    {
        if ($this->isIgnoredUri($page->getUri())) {
            return true;
        }

        if (in_array($page->getPageType(), ['cms_noroute_index', 'cms_noroute_index_*'])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $uri
     * @return bool
     */
    public function isIgnoredUri($uri)
    {
        foreach ($this->getIgnoredUriExpressions() as $expression) {
            if (@preg_match($expression, $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getIgnoredUriExpressions()
    {
        $expressions = [];
        $config      = $this->scopeConfig->getValue(
            'cache_warmer/general/ignored_uri_expressions',
            ScopeInterface::SCOPE_STORE
        );

        try {
            $config = \Zend_Json::decode($config);
        } catch (\Exception $e) {
            $config = @unserialize($config);
        }
        $config = is_array($config) ? $config : [];

        foreach ($config as $item) {
            $expressions[] = $item['expression'];
        }

        return $expressions;
    }

    /**
     * @param string $userAgent
     * @return bool
     */
    public function isIgnoredUserAgent($userAgent)
    {
        foreach ($this->getIgnoredUserAgents() as $expression) {
            if (@preg_match($expression, $userAgent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getIgnoredUserAgents()
    {
        $expressions = [];
        $config      = $this->scopeConfig->getValue(
            'cache_warmer/general/ignored_user_agents',
            ScopeInterface::SCOPE_STORE
        );

        try {
            $config = \Zend_Json::decode($config);
        } catch (\Exception $e) {
            $config = @unserialize($config);
        }
        $config = is_array($config) ? $config : [];

        foreach ($config as $item) {
            $item          = (array)$item;
            $expressions[] = $item['expression'];
        }

        return $expressions;
    }

    /**
     * @return string
     */
    public function getCacheType()
    {
        return $this->scopeConfig->getValue(\Magento\PageCache\Model\Config::XML_PAGECACHE_TYPE);
    }

    /**
     * @return int
     */
    public function getCacheTtl()
    {
        return $this->scopeConfig->getValue(\Magento\PageCache\Model\Config::XML_PAGECACHE_TTL);
    }

    /**
     * @return string
     */
    public function getTmpPath()
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath();

        return $path;
    }

    /**
     * @return string
     */
    public function getWarmerUniquePart()
    {
        return $this->scopeConfig->getValue(
            WarmerServiceInterface::WARMER_UNIQUE_VALUE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isStoreCodeToUrlEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            \Magento\Store\Model\Store::XML_PATH_STORE_IN_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * 100 - no limits
     * @return int
     */
    public function getServerLoadThreshold()
    {
        $value = $this->scopeConfig->getValue('cache_warmer/extended/system_load_limit', ScopeInterface::SCOPE_STORE);

        $value = (int)$value;

        return $value > 25 && $value < 100 ? $value : 100;
    }

    /**
     * @return int
     */
    public function getCacheFillThreshold()
    {
        $value = $this->scopeConfig->getValue('cache_warmer/extended/crawler_limit', ScopeInterface::SCOPE_STORE);

        $value = (int)$value;

        return $value > 0 && $value < 100 ? $value : 100;
    }

    /**
     * @return bool
     */
    public function isDebugToolbarEnabled()
    {
        return $this->scopeConfig->getValue('cache_warmer/debug/info', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getHttpAuthUsername()
    {
        return $this->scopeConfig->getValue(
            'cache_warmer/extended/http_auth/username',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getHttpAuthPassword()
    {
        return $this->scopeConfig->getValue(
            'cache_warmer/extended/http_auth/password',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return integer
     */
    public function isTagLogEnabled()
    {
        return $this->scopeConfig->getValue(
            'cache_warmer/debug/tag_log',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return integer
     */
    public function isBacktraceLogEnabled()
    {
        return $this->scopeConfig->getValue(
            'cache_warmer/debug/backtrace_log',
            ScopeInterface::SCOPE_STORE
        );
    }
}
