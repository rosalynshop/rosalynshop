<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Observer\Review;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Delete
 * @package Amasty\AdvancedReview\Observer\Review
 */
class Delete implements ObserverInterface
{
    /**
     * @var \Amasty\AdvancedReview\Api\ImagesRepositoryInterface
     */
    private $imagesRepository;

    public function __construct(\Amasty\AdvancedReview\Api\ImagesRepositoryInterface $imagesRepository)
    {
        $this->imagesRepository = $imagesRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $review = $observer->getObject();
        if ($review) {
            $this->imagesRepository->deleteByReviewId($review->getId());
        }
    }
}
