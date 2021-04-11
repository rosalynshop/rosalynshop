<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Model\ResourceModel\Wards;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zemi\RegionManager\Api\Data\WardsInterface;

/**
 * Class Collection
 * @package Zemi\RegionManager\Model\ResourceModel\Wards
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = WardsInterface::ID;

    protected $_eventPrefix = 'regionmanager_wards_collection';

    protected $_eventObject = 'wards_collection';

    protected function _construct()
    {
        $this->_init('Zemi\RegionManager\Model\Wards', 'Zemi\RegionManager\Model\ResourceModel\Wards');
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