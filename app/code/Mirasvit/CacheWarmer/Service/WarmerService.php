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



namespace Mirasvit\CacheWarmer\Service;

use Magento\CacheInvalidate\Model\PurgeCache;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\PageCache\Cache;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Service\PageServiceInterface;
use Mirasvit\CacheWarmer\Api\Service\WarmerServiceInterface;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Model\ResourceModel\Page\Collection;
use Mirasvit\CacheWarmer\Service\Config\ExtendedConfig;
use Mirasvit\CacheWarmer\Service\Warmer\PageWarmStatus;
use Mirasvit\CacheWarmer\Api\Data\WarmRuleInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WarmerService implements WarmerServiceInterface
{
    /**
     * @var null|string $userAgentFirstPart
     */
    private static $userAgentFirstPart = null;

    /**
     * @var null|string
     */
    private static $cacheType;


    private $curlService;

    /**
     * @var ExtendedConfig
     */
    private $extendedConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var WarmRuleService
     */
    private $warmRuleService;

    /**
     * @var PageServiceInterface
     */
    private $pageService;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        CurlService $curlService,
        ExtendedConfig $extendedConfig,
        Config $config,
        PageRepositoryInterface $pageRepository,
        PageServiceInterface $pageService,
        WarmRuleService $warmRuleService,
        Cache $cache,
        Context $context,
        StoreManagerInterface $storeManager,
        PageCacheConfig $pageCacheConfig,
        PurgeCache $purgeCache
    ) {
        $this->curlService     = $curlService;
        $this->extendedConfig  = $extendedConfig;
        $this->config          = $config;
        $this->pageRepository  = $pageRepository;
        $this->pageService     = $pageService;
        $this->warmRuleService = $warmRuleService;
        $this->cache           = $cache;
        $this->context         = $context;
        $this->storeManager    = $storeManager;
        $this->pageCacheConfig = $pageCacheConfig;
        $this->purgeCache      = $purgeCache;
    }

    /**
     * @param Collection $collection
     * @param WarmRuleInterface $rule
     * @return \Generator|PageWarmStatus[]
     */
    public function warmCollection(Collection $collection, WarmRuleInterface $rule = null)
    {
        $queue = [];

        /** @var PageInterface $page */
        while ($page = $collection->fetchItem()) {
            $page = $this->warmRuleService->modifyPage($page, $rule);
            if ($this->pageService->isCached($page)) {
                continue;
            }
            $queue[] = $page;

            if (count($queue) >= $this->config->getWarmThreads()) {
                foreach ($this->warmPages($queue) as $warmStatus) {
                   yield $warmStatus;
                }

                $queue = [];
            }
        }

        //if collection more than threads
        if ($queue) {
            foreach ($this->warmPages($queue) as $warmStatus) {
                yield $warmStatus;
            }
        }
    }

    /**
     * @param PageInterface[] $pages
     * @return PageWarmStatus[]
     */
    private function warmPages($pages)
    {
        $channels = $this->curlService->initMultiChannel(count($pages));

        foreach ($channels as $idx => $channel) {
            $page = $pages[$idx];

            $userAgent = $this->getUserAgent(
                $page->getVaryData(),
                $page->getProductId(),
                $page->getCategoryId()
            );

            $channel->setUrl($page->getUri());
            $channel->setUserAgent($userAgent);
            $channel->setHeaders($page->getHeaders());
            //we add this cookie to have correct cache hit stats
            $channel->addCookie('mst-cache-warmer-track', 1);
            if ($page->getVaryString()) {
                $channel->addCookie('X-Magento-Vary', $page->getVaryString());
            }
        }

        $result = [];
        foreach ($this->curlService->multiRequest($channels) as $idx => $response) {
            $result[] = new PageWarmStatus($pages[$idx], $response);
        }

        return $result;
    }

    /**
     * @param array $varyData
     * @param int   $productId
     * @param int   $categoryId
     * @return string
     */
    public function getUserAgent($varyData, $productId, $categoryId)
    {
        $agent = $this->getUserAgentFirstPart();
        $agent .= base64_encode(serialize($varyData));
        $agent .= WarmerServiceInterface::PRODUCT_BEGIN_TAG
            . $productId . WarmerServiceInterface::PRODUCT_END_TAG;
        $agent .= WarmerServiceInterface::CATEGORY_BEGIN_TAG
            . $categoryId . WarmerServiceInterface::CATEGORY_END_TAG;

        return $agent;
    }

    /**
     * @return null|string
     */
    private function getUserAgentFirstPart()
    {
        if (self::$userAgentFirstPart === null) {
            self::$userAgentFirstPart = WarmerServiceInterface::USER_AGENT . $this->getWarmerUniquePart() . ':';
        }

        return self::$userAgentFirstPart;
    }

    /**
     * Create unique warmer user agent for security reason
     * @return string
     */
    private function getWarmerUniquePart()
    {
        return $this->config->getWarmerUniquePart();
    }

    /**
     * {@inheritdoc}
     */
    public function cleanPage(PageInterface $page)
    {
        if ($this->getCacheType() == PageCacheConfig::BUILT_IN) {
            if ($page->getCacheId()) {
                $this->cache->remove($page->getCacheId());
            }
        } else {
            $tags    = [];
            $pattern = "((^|,)%s(,|$))";
            if ($page->getProductId()) {
                $tags[] = 'cat_p_' . $page->getProductId();
            }
            if ($page->getCategoryId()) {
                $tags[] = 'cat_c_' . $page->getCategoryId();
                $tags[] = 'cat_c_p_' . $page->getCategoryId();
            }
            foreach ($tags as $key => $tag) {
                $tags[$key] = sprintf($pattern, $tag);
            }
            if (!empty($tags)) {
                $this->purgeCache->sendPurgeRequest(implode('|', array_unique($tags)));
            }
        }

        return true;
    }

    /**
     * @return string
     */
    private function getCacheType()
    {
        if (self::$cacheType === null) {
            self::$cacheType = $this->config->getCacheType();
        }

        return self::$cacheType;
    }

    /**
     * @return bool|array
     */
    public function getVaryData()
    {
        $agent = $this->context->getHttpHeader()->getHttpUserAgent();

        if (strpos($agent, $this->getUserAgentFirstPart()) !== false) {
            $data = str_replace($this->getUserAgentFirstPart(), '', $agent);
            $data = preg_replace('/' . WarmerServiceInterface::PRODUCT_BEGIN_TAG
                . '(.*?)' . WarmerServiceInterface::PRODUCT_END_TAG . '/ims', '', $data);
            $data = preg_replace('/' . WarmerServiceInterface::CATEGORY_BEGIN_TAG
                . '(.*?)' . WarmerServiceInterface::CATEGORY_END_TAG . '/ims', '', $data);
            $data = unserialize(base64_decode($data));

            return $data;
        }

        return false;
    }

    /**
     * @return bool|array
     */
    public function getProductId()
    {
        $agent = $this->context->getHttpHeader()->getHttpUserAgent();

        if (strpos($agent, $this->getUserAgentFirstPart()) !== false) {
            preg_match('/' . WarmerServiceInterface::PRODUCT_BEGIN_TAG
                . '(.*?)' . WarmerServiceInterface::PRODUCT_END_TAG . '/ims', $agent, $data);

            return (isset($data[1])) ? $data[1] : false;
        }

        return false;
    }

    /**
     * @return bool|array
     */
    public function getCategoryId()
    {
        $agent = $this->context->getHttpHeader()->getHttpUserAgent();

        if (strpos($agent, $this->getUserAgentFirstPart()) !== false) {
            preg_match('/' . WarmerServiceInterface::CATEGORY_BEGIN_TAG
                . '(.*?)' . WarmerServiceInterface::CATEGORY_END_TAG . '/ims', $agent, $data);

            return (isset($data[1])) ? $data[1] : false;
        }

        return false;
    }
}
