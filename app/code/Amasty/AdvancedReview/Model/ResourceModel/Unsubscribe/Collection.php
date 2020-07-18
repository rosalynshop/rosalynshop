<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe;

use Amasty\AdvancedReview\Model\Unsubscribe;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe
 */
class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            Unsubscribe::class,
            \Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe::class
        );
    }
}
