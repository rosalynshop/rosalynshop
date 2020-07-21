<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Repository;

use Amasty\XmlSitemap\Api\SitemapInterface;
use Amasty\XmlSitemap\Api\SitemapRepositoryInterface;
use Amasty\XmlSitemap\Model\SitemapFactory;
use Amasty\XmlSitemap\Model\ResourceModel\Sitemap as SitemapResource;
use Amasty\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory;
use Amasty\XmlSitemap\Model\ResourceModel\Sitemap\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SitemapRepository implements SitemapRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var SitemapFactory
     */
    private $sitemapFactory;

    /**
     * @var SitemapResource
     */
    private $sitemapResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $sitemaps;

    /**
     * @var CollectionFactory
     */
    private $sitemapCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        SitemapFactory $sitemapFactory,
        SitemapResource $sitemapResource,
        CollectionFactory $sitemapCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->sitemapFactory = $sitemapFactory;
        $this->sitemapResource = $sitemapResource;
        $this->sitemapCollectionFactory = $sitemapCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(SitemapInterface $sitemap)
    {
        try {
            if ($sitemap->getId()) {
                $sitemap = $this->getById($sitemap->getId())->addData($sitemap->getData());
            }
            $this->sitemapResource->save($sitemap);
            unset($this->sitemaps[$sitemap->getId()]);
        } catch (\Exception $e) {
            if ($sitemap->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save sitemap with ID %1. Error: %2',
                        [$sitemap->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new sitemap. Error: %1', $e->getMessage()));
        }

        return $sitemap;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->sitemaps[$id])) {
            /** @var \Amasty\XmlSitemap\Model\Sitemap $sitemap */
            $sitemap = $this->sitemapFactory->create();
            $this->sitemapResource->load($sitemap, $id);
            if (!$sitemap->getId()) {
                throw new NoSuchEntityException(__('Sitemap with specified ID "%1" not found.', $id));
            }
            $this->sitemaps[$id] = $sitemap;
        }

        return $this->sitemaps[$id];
    }

    /**
     * @inheritdoc
     */
    public function delete(SitemapInterface $sitemap)
    {
        try {
            $this->sitemapResource->delete($sitemap);
            unset($this->sitemaps[$sitemap->getId()]);
        } catch (\Exception $e) {
            if ($sitemap->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove sitemap with ID %1. Error: %2',
                        [$sitemap->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove sitemap. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $sitemapModel = $this->getById($id);
        $this->delete($sitemapModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\XmlSitemap\Model\ResourceModel\Sitemap\Collection $sitemapCollection */
        $sitemapCollection = $this->sitemapCollectionFactory->create();
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $sitemapCollection);
        }
        $searchResults->setTotalCount($sitemapCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $sitemapCollection);
        }
        $sitemapCollection->setCurPage($searchCriteria->getCurrentPage());
        $sitemapCollection->setPageSize($searchCriteria->getPageSize());
        $sitemaps = [];
        /** @var SitemapInterface $sitemap */
        foreach ($sitemapCollection->getItems() as $sitemap) {
            $sitemaps[] = $this->getById($sitemap->getId());
        }
        $searchResults->setItems($sitemaps);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $sitemapCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $sitemapCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $sitemapCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
    * Helper function that adds a SortOrder to the collection.
    *
    * @param SortOrder[] $sortOrders
    * @param Collection  $sitemapCollection
    *
    * @return void
    */
    private function addOrderToCollection($sortOrders, Collection $sitemapCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $sitemapCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }
}
