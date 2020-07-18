<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block;

use Amasty\AdvancedReview\Model\ResourceModel\Images\Collection;
use Magento\Framework\View\Element\Template;
use Amasty\AdvancedReview\Model\ResourceModel\Images\CollectionFactory;

/**
 * Class Images
 * @package Amasty\AdvancedReview\Block
 */
class Images extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::images.phtml';

    /**
     * @var int
     */
    private $reviewId;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Amasty\AdvancedReview\Helper\ImageHelper
     */
    private $imageHelper;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        \Amasty\AdvancedReview\Helper\Config $configHelper,
        \Amasty\AdvancedReview\Helper\ImageHelper $imageHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonEncoder = $jsonEncoder;
        $this->collectionFactory = $collectionFactory;
        $this->configHelper = $configHelper;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('review_id', $this->getReviewId());

        return $collection;
    }

    /**
     * @param $item
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFullImagePath($item)
    {
        return $this->imageHelper->getFullPath($item->getPath());
    }

    /**
     * @param $item
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResizedImagePath($item)
    {
        return $this->imageHelper->resize($item->getPath(), $this->configHelper->getReviewImageWidth() * 2);
    }

    /**
     * @return string
     */
    public function getMaxHeight()
    {
        return $this->configHelper->getReviewImageWidth();
    }

    /**
     * @return int
     */
    public function getReviewId()
    {
        return $this->reviewId;
    }

    /**
     * @param $reviewId
     *
     * @return $this
     */
    public function setReviewId($reviewId)
    {
        $this->reviewId = $reviewId;
        return $this;
    }
}
