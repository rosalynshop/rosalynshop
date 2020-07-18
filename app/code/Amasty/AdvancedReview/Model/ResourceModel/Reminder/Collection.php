<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Reminder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Amasty\AdvancedReview\Model\ResourceModel\Reminder
 */
class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            \Amasty\AdvancedReview\Model\Reminder::class,
            \Amasty\AdvancedReview\Model\ResourceModel\Reminder::class
        );
        $this->_idFieldName = 'entity_id';
    }
}
