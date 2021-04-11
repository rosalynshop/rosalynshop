<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Block\Adminhtml\System\Wards;

use Magento\Framework\DataObject;
use Magento\Framework\Option\ArrayInterface;
use Zemi\RegionManager\Model\ResourceModel\Cities\CollectionFactory as CitiesFactory;

class SelectWards extends DataObject implements ArrayInterface
{
    /**
     * @var CitiesFactory
     */
    protected $_citiesCollection;

    /**
     * SelectCities constructor.
     * @param CitiesFactory $citiesCollection
     * @param array $data
     */
    public function __construct(
        CitiesFactory $citiesCollection,
        array $data = []
    ){
        $this->_citiesCollection = $citiesCollection;
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
        $collection = $this->_citiesCollection->create()->getData();
        $arr = [];
        foreach ($collection as $cities) {
            $arr[] = [
                'label' => __($cities['cities_name']),
                'value' => $cities['cities_name']
            ];
        }
        return $arr;
    }
}