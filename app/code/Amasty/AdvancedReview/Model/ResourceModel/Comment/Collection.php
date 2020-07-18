<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Comment;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Model\Comment;
use Amasty\AdvancedReview\Model\ResourceModel\Comment as CommentResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Comment::class, CommentResource::class);
        $this->_setIdFieldName(CommentInterface::ID);
    }
}
