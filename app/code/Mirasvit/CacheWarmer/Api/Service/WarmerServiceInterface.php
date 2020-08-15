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



namespace Mirasvit\CacheWarmer\Api\Service;

use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Model\ResourceModel\Page\Collection;
use Mirasvit\CacheWarmer\Service\Warmer\PageWarmStatus;
use Mirasvit\CacheWarmer\Api\Data\WarmRuleInterface;

interface WarmerServiceInterface
{
    const USER_AGENT          = 'CacheWarmer';
    const STATUS_USER_AGENT   = 'CacheWarmerStatus';
    const WARMER_UNIQUE_VALUE = 'cache_warmer/unique_value';

    const PRODUCT_BEGIN_TAG  = 'prod_id_begin_';
    const PRODUCT_END_TAG    = '_prod_id_end';
    const CATEGORY_BEGIN_TAG = 'cat_id_begin_';
    const CATEGORY_END_TAG   = '_cat_id_end';

    //    /**
    //     * @param bool $isWarmMobilePages
    //     * @param PageInterface $page
    //     * @return bool
    //     */
    //    public function warmPage(PageInterface $page, $isWarmMobilePages = false);
    //
    //    /**
    //     * @param PageInterface[] $pages
    //     * @return bool
    //     */
    //    public function warmPages($pages);

    /**
     * @param Collection $collection
     * @param WarmRuleInterface $rule
     * @return PageWarmStatus[]
     */
    public function warmCollection(Collection $collection, WarmRuleInterface $rule = null);

    //    /**
    //     * @param string $uri
    //     * @param bool|int $productId
    //     * @param bool|int $categoryId
    //     * @return bool
    //     */
    //    public function warmUrl($uri, $productId, $categoryId);

    /**
     * @param PageInterface $page
     * @return bool
     */
    public function cleanPage(PageInterface $page);

    //    /**
    //     * @return array|false
    //     */
    //    public function getVaryData();
}
