<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Model\ResourceModel\States;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zemi\RegionManager\Api\Data\StatesInterface;

/**
 * Class Collection
 * @package Zemi\RegionManager\Model\ResourceModel\States
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = StatesInterface::ID;

    protected $_eventPrefix = 'regionmanager_states_collection';

    protected $_eventObject = 'states_collection';

    protected function _construct()
    {
        $this->_init('Zemi\RegionManager\Model\States', 'Zemi\RegionManager\Model\ResourceModel\States');
    }

    /**
     * Overridden to use other label field by default.
     *
     * @param null $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = null, $labelField = 'title', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}