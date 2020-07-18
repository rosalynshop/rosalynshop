<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Images;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Amasty\AdvancedReview\Model\ResourceModel\Images
 */
class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            \Amasty\AdvancedReview\Model\Images::class,
            \Amasty\AdvancedReview\Model\ResourceModel\Images::class
        );
    }

    /**
     * @return array
     */
    public function getImageKeys()
    {
        $this->getSelect()->columns('CONCAT(review_id,path) as image_key');

        return $this->getColumnValues('image_key');
    }
}
