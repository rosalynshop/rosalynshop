<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Option\ArrayInterface;

class CommentStatus implements ArrayInterface
{
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => self::STATUS_APPROVED, 'label' => __('Approved')],
            ['value' => self::STATUS_REJECTED, 'label' => __('Rejected')]
        ];
    }
}
