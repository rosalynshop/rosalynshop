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



namespace Mirasvit\CacheWarmer\Repository;

use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Data\PageInterfaceFactory;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Model\ResourceModel\Page\CollectionFactory;

class PageRepository implements PageRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var PageInterfaceFactory
     */
    private $pageFactory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        PageInterfaceFactory $pageFactory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->pageFactory       = $pageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $page = $this->create();
        $page = $this->entityManager->load($page, $id);

        if (!$page->getId()) {
            return false;
        }

        return $page;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->pageFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getByCacheId($cacheId)
    {
        $page = $this->create();
        $page = $page->load($cacheId, PageInterface::CACHE_ID);
        //M2.1 return 0, M2.2 return 1
        if (!$page->getId() || $page->getId() == 1) {
            return false;
        }

        return $page;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PageInterface $page)
    {
        $this->entityManager->delete($page);
    }

    /**
     * {@inheritdoc}
     */
    public function save(PageInterface $page)
    {
        return $this->entityManager->save($page);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTypes()
    {
        $select = clone $this->getCollection()->getSelect();
        $select->reset(Select::ORDER)
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET)
            ->reset(Select::COLUMNS)
            ->group(PageInterface::PAGE_TYPE)
            ->columns(PageInterface::PAGE_TYPE);

        return $this->getCollection()->getConnection()->fetchCol($select);
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }
}