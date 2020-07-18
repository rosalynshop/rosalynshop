<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Repository;

use Amasty\AdvancedReview\Api\Data;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Amasty\AdvancedReview\Model\ResourceModel;
use Amasty\AdvancedReview\Model\ImagesFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Amasty\AdvancedReview\Helper\ImageHelper;

/**
 * Class ImagesRepository
 * @package Amasty\AdvancedReview\Model\Repository
 */
class ImagesRepository implements \Amasty\AdvancedReview\Api\ImagesRepositoryInterface
{
    /**
     * @var array
     */
    private $images = [];

    /**
     * @var ResourceModel\Images
     */
    private $imagesResource;

    /**
     * @var ImagesFactory
     */
    private $imagesFactory;

    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ResourceModel\Images\CollectionFactory
     */
    private $imagesCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @var null|string
     */
    private $folderPath = null;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $configHelper;

    public function __construct(
        \Amasty\AdvancedReview\Model\ResourceModel\Images $imagesResource,
        \Amasty\AdvancedReview\Model\ImagesFactory $imagesFactory,
        \Amasty\AdvancedReview\Model\ResourceModel\Images\CollectionFactory $imagesCollectionFactory,
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Amasty\AdvancedReview\Helper\Config $configHelper
    ) {
        $this->imagesResource = $imagesResource;
        $this->imagesFactory = $imagesFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->imagesCollectionFactory = $imagesCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ioFile = $ioFile;
        $this->filesystem = $filesystem;
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\ImagesInterface $image)
    {
        if ($image->getImageId()) {
            $image = $this->get($image->getImageId())->addData($image->getData());
        }

        try {
            $this->imagesResource->save($image);
            $this->images[$image->getImageId()] = $image;
        } catch (\Exception $e) {
            if ($image->getImageId()) {
                throw new CouldNotSaveException(
                    __('Unable to save image with ID %1. Error: %2', [$image->getImageId(), $e->getMessage()])
                );
            }
            throw new CouldNotSaveException(__('Unable to save new image. Error: %1', $e->getMessage()));
        }
        
        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function get($imageId)
    {
        if (!isset($this->images[$imageId])) {
            /** @var \Amasty\AdvancedReview\Model\Images $image */
            $image = $this->imagesFactory->create();
            $this->imagesResource->load($image, $imageId);
            if (!$image->getImageId()) {
                throw new NoSuchEntityException(__('Rule with specified ID "%1" not found.', $imageId));
            }
            $this->images[$imageId] = $image;
        }
        return $this->images[$imageId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Data\ImagesInterface $image)
    {
        try {
            $this->ioFile->rm($this->getFolderPath() . $image->getPath());
            $this->ioFile->rm(
                $this->getFolderPath() . 'resized/' . $this->configHelper->getReviewImageWidth() . $image->getPath()
            );
            $this->imagesResource->delete($image);
            unset($this->images[$image->getId()]);
        } catch (\Exception $e) {
            if ($image->getImageId()) {
                throw new CouldNotDeleteException(
                    __('Unable to remove image with ID %1. Error: %2', [$image->getImageId(), $e->getMessage()])
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove image. Error: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @return string
     */
    private function getFolderPath()
    {
        if ($this->folderPath === null) {
            $this->folderPath = $this->filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(
                ImageHelper::IMAGE_PATH
            );
        }

        return $this->folderPath;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($imageId)
    {
        $model = $this->get($imageId);
        $this->delete($model);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\AdvancedReview\Model\ResourceModel\Reminder\Collection $reminderCollection */
        $imagesCollection = $this->imagesCollectionFactory->create();
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $imagesCollection);
        }

        $searchResults->setTotalCount($imagesCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $imagesCollection);
        }

        $imagesCollection->setCurPage($searchCriteria->getCurrentPage());
        $imagesCollection->setPageSize($searchCriteria->getPageSize());
        $images = [];

        /** @var \Amasty\AdvancedReview\Model\Images $reminder */
        foreach ($imagesCollection->getItems() as $image) {
            $images[] = $this->get($image->getId());
        }
        $searchResults->setItems($images);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param ResourceModel\Images\Collection  $imageCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        ResourceModel\Images\Collection $imageCollection
    ) {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $imageCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param ResourceModel\Images\Collection  $imageCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, ResourceModel\Images\Collection $imageCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $imageCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }

    /**
     * @param $reviewId
     *
     * @return bool
     */
    public function deleteByReviewId($reviewId)
    {
        $this->searchCriteriaBuilder->addFilter('review_id', $reviewId);
        foreach ($this->getList($this->searchCriteriaBuilder->create())->getItems() as $review) {
            $this->delete($review);
        }

        return true;
    }

    /**
     * @return \Amasty\AdvancedReview\Model\Images
     */
    public function getImageModel()
    {
        return $this->imagesFactory->create();
    }

    /**
     * @return array
     */
    public function getImageKeys()
    {
        return $this->imagesCollectionFactory->create()->getImageKeys();
    }
}
