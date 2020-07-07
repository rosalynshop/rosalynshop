<?php
/**
 * @author   Rosalynshop <info@rosalynshop.com>
 * @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Block\Adminhtml\System\Cities;

use Magento\Framework\DataObject;
use Magento\Framework\Option\ArrayInterface;
use RosalynShop\RegionManager\Model\ResourceModel\States\CollectionFactory as StatesFactory;

class SelectCities extends DataObject implements ArrayInterface
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
    ) {
        $this->_statesCollection = $statesCollection;
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
        foreach ($collection as $state) {
            $arr[] = [
                'label' => __($state['states_name']),
                'value' => $state['states_name']
            ];
        }
        return $arr;
    }
}