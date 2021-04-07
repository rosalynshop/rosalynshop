<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Block\Adminhtml\System\Cities;

use Zemi\RegionManager\Model\ResourceModel\States\CollectionFactory as StatesFactory;

/**
 * Class SelectCities
 * @package Zemi\RegionManager\Block\Adminhtml\System\Cities
 */
class SelectCities extends \Magento\Framework\DataObject implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var StatesFactory
     */
    protected $_statesCollection;

    /**
     * SelectCities constructor.
     * @param StatesFactory $statesCollection
     * @param array $data
     */
    public function __construct(
        StatesFactory $statesCollection,
        array $data = []
    ){
        $this->_statesCollection    = $statesCollection;
        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getArray();
    }

    /**
     * @return array
     */
    public function getArray()
    {
        $collection = $this->_statesCollection->create()->getData();
        $arr = [];
        foreach ($collection as $state)
        {
            $arr[] = ['label' => __($state['states_name']) ,'value' => $state['states_name']];
        }
        return $arr;
    }
}