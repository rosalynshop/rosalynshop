<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel;

/**
 * Class Vote
 * @package Amasty\AdvancedReview\Model\ResourceModel
 */
class Vote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'amasty_advanced_review_vote';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'vote_id');
    }
}
