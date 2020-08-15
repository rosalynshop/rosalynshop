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

use Magento\Framework\View\Model\Layout\Merge as LayoutMerge;
use Mirasvit\CacheWarmer\Model\Config\Source\PageCacheable;
use Mirasvit\CacheWarmer\Service\Config\ExtendedConfig;

class DeleteCacheableFalse extends LayoutMerge
{
    const ALLOWED_ACTIONS
        = ['cms_index_index',
           'catalog_product_view',
           'catalog_category_view',
        ];

    const CACHEABLE_FALSE = 'cacheable="false"';


    public function __construct(
        ExtendedConfig $extendedConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Url\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\View\File\CollectorInterface $fileSource,
        \Magento\Framework\View\File\CollectorInterface $pageLayoutFileSource,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Magento\Framework\View\Model\Layout\Update\Validator $validator,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem\File\ReadFactory $readFactory,
        \Magento\Framework\View\Design\ThemeInterface $theme = null,
        $cacheSuffix = ''
    ) {
        $this->extendedConfig = $extendedConfig;
        $this->request        = $request;
        parent::__construct($design,
            $scopeResolver,
            $fileSource,
            $pageLayoutFileSource,
            $appState,
            $cache,
            $validator,
            $logger,
            $readFactory,
            $theme,
            $cacheSuffix
        );
    }

    /**
     * Get all registered updates as string
     * @return string
     */
    public function asString()
    {
        $updates = implode('', $this->updates);
        $updates = $this->getPreparedUpdates($updates);

        return $updates;
    }

    /**
     * @param string $updates
     * @return string
     */
    protected function getPreparedUpdates($updates)
    {
        if ($this->extendedConfig->isDeleteCacheableFalse() == PageCacheable::PAGE_CACHEABLE_CONFIGURE) {
            $allowedActions = $this->extendedConfig->getDeleteCacheableFalseConfig();
        } else {
            $allowedActions = self::ALLOWED_ACTIONS;
        }
        if ($this->extendedConfig->isDeleteCacheableFalse()
            && in_array($this->request->getFullActionName(), $allowedActions)) {
            $updates = str_replace(self::CACHEABLE_FALSE, '', $updates);
        }

        return $updates;
    }
}