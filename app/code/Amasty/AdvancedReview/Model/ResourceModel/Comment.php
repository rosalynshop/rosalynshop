<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Comment extends AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CommentInterface::TABLE, CommentInterface::ID);
    }
}
