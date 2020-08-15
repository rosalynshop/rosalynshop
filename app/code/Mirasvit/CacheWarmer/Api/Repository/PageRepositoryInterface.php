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



namespace Mirasvit\CacheWarmer\Api\Repository;

use Mirasvit\CacheWarmer\Api\Data\PageInterface;

interface PageRepositoryInterface
{
    const POPULARITY_ADDED = 'm__warmer_popularity_added';

    /**
     * @return \Mirasvit\CacheWarmer\Model\ResourceModel\Page\Collection|PageInterface[]
     */
    public function getCollection();

    /**
     * @return PageInterface
     */
    public function create();

    /**
     * @param PageInterface $page
     * @return PageInterface
     */
    public function save(PageInterface $page);

    /**
     * @param int $id
     * @return PageInterface|false
     */
    public function get($id);

    /**
     * @param string $cacheId
     * @return PageInterface|false
     */
    public function getByCacheId($cacheId);

    /**
     * @param PageInterface $page
     * @return bool
     */
    public function delete(PageInterface $page);

    /**
     * @return array
     */
    public function getPageTypes();
}