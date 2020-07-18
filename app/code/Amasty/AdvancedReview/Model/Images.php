<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model;

use Amasty\AdvancedReview\Api\Data\ImagesInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Images
 * @package Amasty\AdvancedReview\Model
 */
class Images extends AbstractModel implements ImagesInterface
{
    public function _construct()
    {
        $this->_init(\Amasty\AdvancedReview\Model\ResourceModel\Images::class);
    }

    /**
     * Returns image id field
     *
     * @return int|null
     */
    public function getImageId()
    {
        return $this->getData(self::IMAGE_ID);
    }

    /**
     * @param int $imageId
     *
     * @return $this
     */
    public function setImageId($imageId)
    {
        $this->setData(self::IMAGE_ID, $imageId);
        return $this;
    }

    /**
     * Returns review id field
     *
     * @return int|null
     */
    public function getReviewId()
    {
        return $this->getData(self::REVIEW_ID);
    }

    /**
     * @param int $reviewId
     *
     * @return $this
     */
    public function setReviewId($reviewId)
    {
        $this->setData(self::REVIEW_ID, $reviewId);
        return $this;
    }

    /**
     * Returns image path
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->getData(self::PATH);
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->setData(self::PATH, $path);
        return $this;
    }
}
