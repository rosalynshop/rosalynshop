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



namespace Mirasvit\CacheWarmer\Api\Data;

interface PageInterface
{
    const TABLE_NAME = 'mst_cache_warmer_page';

    const ID            = 'page_id';
    const URI           = 'uri';
    const CACHE_ID      = 'cache_id';
    const PAGE_TYPE     = 'page_type';
    const PRODUCT_ID    = 'product_id';
    const CATEGORY_ID   = 'category_id';
    const VARY_DATA     = 'vary_data';
    const ATTEMPTS      = 'attempts';
    const POPULARITY    = 'popularity';
    const WARM_RULE_VERSION  = 'warm_rule_version';
    const WARM_RULE_IDS = 'warm_rule_ids';
    const HEADERS       = 'headers';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getUri();

    /**
     * @param string $value
     * @return $this
     */
    public function setUri($value);

    /**
     * @return string
     */
    public function getCacheId();

    /**
     * @param string $value
     * @return $this
     */
    public function setCacheId($value);

    /**
     * @return string
     */
    public function getPageType();

    /**
     * @param string $value
     * @return $this
     */
    public function setPageType($value);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $value
     * @return $this
     */
    public function setProductId($value);

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $value
     * @return $this
     */
    public function setCategoryId($value);

    /**
     * @return array
     */
    public function getVaryData();

    /**
     * @param string|array $value
     * @return $this
     */
    public function setVaryData($value);

    /**
     * @return string
     */
    public function getVaryString();

    /**
     * @return int
     */
    public function getAttempts();

    /**
     * @param int $value
     * @return $this
     */
    public function setAttempts($value);

    /**
     * @return int
     */
    public function getPopularity();

    /**
     * @param int $value
     * @return $this
     */
    public function setPopularity($value);

    /**
     * @return string
     */
    public function getWarmRuleVersion();

    /**
     * @param string $value
     * @return $this
     */
    public function setWarmRuleVersion($value);

    /**
     * @return array
     */
    public function getWarmRuleIds();

    /**
     * @param array $value
     * @return $this
     */
    public function setWarmRuleIds(array $value);

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @param array $value
     * @return $this
     */
    public function setHeaders(array $value);
}