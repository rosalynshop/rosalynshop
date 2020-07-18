<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Api\Data;

interface ImagesInterface
{
    const IMAGE_ID = 'image_id';

    const REVIEW_ID = 'review_id';

    const PATH = 'path';

    /**
     * Returns image id field
     *
     * @return int|null
     */
    public function getImageId();

    /**
     * @param int $imageId
     *
     * @return $this
     */
    public function setImageId($imageId);

    /**
     * Returns review id field
     *
     * @return int|null
     */
    public function getReviewId();

    /**
     * @param int $reviewId
     *
     * @return $this
     */
    public function setReviewId($reviewId);

    /**
     * Returns image path
     *
     * @return string|null
     */
    public function getPath();

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path);
}
