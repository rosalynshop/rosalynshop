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



namespace Mirasvit\CacheWarmer\Preference;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\Cache\Frontend\Decorator\TagScope as DecoratorTagScope;
use Magento\Framework\Cache\FrontendInterface;
use Mirasvit\CacheWarmer\Logger\Logger;
use Mirasvit\CacheWarmer\Service\Config\DebugConfig;
use Mirasvit\CacheWarmer\Service\Config\ExtendedConfig;

class CacheTagScope extends DecoratorTagScope
{
    const ALLOWED_ACTIONS = ['adminhtml_cache_flushSystem', 'adminhtml_cache_flushAll'];

    protected static $extendedConfig = null;

    protected static $debugConfig    = null;

    public function __construct(
        FrontendInterface $frontend,
        Logger $logger,
        RequestHttp $request,
        $tag
    ) {
        $this->logger  = $logger;
        $this->request = $request;
        parent::__construct($frontend, $tag);
    }

    /**
     * Limit the cleaning scope within a tag
     * {@inheritdoc}
     */
    public function clean($mode = \Zend_Cache::CLEANING_MODE_ALL, array $tags = [])
    {
        echo __METHOD__;
        $isConnectAllowed = true;
        if (PHP_SAPI == 'cli' && !$tags) {
            $isConnectAllowed = false; //ability run setup:di:compile without database
        }

        if ($isConnectAllowed
            && $this->isCleanCacheForbidden($tags)
            && !in_array($this->request->getFullActionName(), self::ALLOWED_ACTIONS)) {
            return false;
        }
        if ($isConnectAllowed) {
            $this->cacheCleanLog($tags);
        }

        if ($mode == \Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG) {
            $result = false;
            foreach ($tags as $tag) {
                if (parent::clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [$tag, $this->getTag()])) {
                    $result = true;
                }
            }
        } else {
            if ($mode == \Zend_Cache::CLEANING_MODE_ALL) {
                $mode = \Zend_Cache::CLEANING_MODE_MATCHING_TAG;
                $tags = [$this->getTag()];
            } else {
                $tags[] = $this->getTag();
            }
            $result = parent::clean($mode, $tags);
        }

        return $result;
    }

    /**
     * Check if cache flushing forbidden
     * @param array $tags
     * @return bool
     */
    protected function isCleanCacheForbidden($tags)
    {
        if ($this->getExtendedConfig()->isForbidCacheFlush()) {
            $isCleanForbidden = true;
            $cacheArray       = ['FPC',
                                 'CONFIG',
                                 'LAYOUT_GENERAL_CACHE_TAG',
                                 'BLOCK_HTML',
                                 'COLLECTION_DATA',
                                 'REFLECTION',
                                 'DB_DDL',
                                 'EAV',
                                 'CUSTOMER_NOTIFICATION',
                                 'INTEGRATION',
                                 'INTEGRATION_API_CONFIG',
                                 'TRANSLATE',
                                 'WEBSERVICE',
            ];

            //allow flush FPC from Cache Management
            if (count($tags) == 1 && isset($tags[0]) && in_array($tags[0], $cacheArray)) {
                $isCleanForbidden = false;
            }

            return $isCleanForbidden;
        }

        return false;
    }

    /**
     * Prevent error "Cache frontend 'default' is not recognized." (for some stores)
     * @return ExtendedConfig
     */
    protected function getExtendedConfig()
    {
        if (self::$extendedConfig === null) {
            self::$extendedConfig = ObjectManager::getInstance()
                ->get(ExtendedConfig::class);
        }

        return self::$extendedConfig;
    }

    /**
     * Check reindex and Cache Management cache flushing
     * @param array $tags
     * @return void
     */
    protected function cacheCleanLog($tags)
    {
        if (($preparedTags = implode(' | ', $tags))
            && ($preparedTags == 'FPC'
                || strpos($preparedTags, 'catalog_product | FPC') !== false
                || strpos($preparedTags, 'catalog_category | FPC') !== false)) {
            if ($this->getDebugConfig()->isTagLogEnabled()) {
                $this->logger->info('FLUSH TAGS: ' . $preparedTags);
            }
            if ($this->getDebugConfig()->isBacktraceLogEnabled()) {
                $this->logger->info(\Magento\Framework\Debug::backtrace(true, false, false));
            }
        }
    }

    /**
     * Prevent error "Cache frontend 'default' is not recognized." (for some stores)
     * @return DebugConfig
     */
    protected function getDebugConfig()
    {
        if (self::$debugConfig === null) {
            self::$debugConfig = ObjectManager::getInstance()
                ->get(DebugConfig::class);
        }

        return self::$debugConfig;
    }
}
