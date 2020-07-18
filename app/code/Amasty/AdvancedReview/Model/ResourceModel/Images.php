<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel;

/**
 * Class Images
 * @package Amasty\AdvancedReview\Model\ResourceModel
 */
class Images extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'amasty_advanced_review_images';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'image_id');
    }
}
