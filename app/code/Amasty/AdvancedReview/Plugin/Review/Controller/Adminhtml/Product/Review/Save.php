<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\Review\Controller\Adminhtml\Product\Review;

use Magento\Review\Controller\Adminhtml\Product\Save as MagentoReview;

/**
 * Class Save
 * @package Amasty\AdvancedReview\Plugin\Review\Controller\Adminhtml\Product\Review
 */
class Save
{
    /**
     * @var \Amasty\AdvancedReview\Model\Repository\ImagesRepository
     */
    private $imagesRepository;

    public function __construct(
        \Amasty\AdvancedReview\Model\Repository\ImagesRepository $imagesRepository
    ) {
        $this->imagesRepository = $imagesRepository;
    }

    /**
     * @param MagentoReview $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        MagentoReview $subject,
        $result
    ) {
        $this->removeImages($subject);

        return $result;
    }

    /**
     * @param MagentoReview $subject
     */
    private function removeImages(MagentoReview $subject)
    {
        $images = $subject->getRequest()->getParam('review_remove_image', []);
        if (is_array($images)) {
            foreach ($images as $id => $image) {
                $this->imagesRepository->deleteById($id);
            }
        }
    }
}
