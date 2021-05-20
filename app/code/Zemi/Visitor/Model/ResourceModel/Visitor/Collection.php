<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\Visitor\Model\ResourceModel\Visitor;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zemi\Visitor\Api\Data\VisitorInterface;

/**
 * Class Collection
 * @package Zemi\Visitor\Model\ResourceModel\Visitor
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = VisitorInterface::ID;

    protected $_eventPrefix = 'zm_visitor_customer';

    protected $_eventObject = 'zm_visitor_customer';

    protected function _construct()
    {
        $this->_init('Zemi\Visitor\Model\Visitor', 'Zemi\Visitor\Model\ResourceModel\Visitor');
    }
}
