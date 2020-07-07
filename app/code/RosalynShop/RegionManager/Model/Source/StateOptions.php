<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Model\Source;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class StateOptions
 * @package RosalynShop\RegionManager\Model\Source
 */
class StateOptions implements ArrayInterface
{
    /**
     * @var ResourceConnection
     */
    private $_resource;

    /**
     * StateOptions constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ){
        $this->_resource = $resource;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getStates() as $field) {
            $options[] = ['label' => $field['states_name'], 'value' => $field['entity_id']];
        }
        return $options;
    }

    public function getStates()
    {
        $adapter = $this->_resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $select = $adapter->select()
            ->from('regionmanager_states');
        return $adapter->fetchAll($select);
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Yes')];
    }
}
