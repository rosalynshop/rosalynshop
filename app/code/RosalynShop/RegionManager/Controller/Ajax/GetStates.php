<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Controller\Ajax;

use Magento\Framework\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use RosalynShop\RegionManager\Model\Config;
use RosalynShop\RegionManager\Model\ResourceModel\States\CollectionFactory as StatesCollection;

/**
 * Class GetStates
 * @package RosalynShop\RegionManager\Controller\Ajax
 */
class GetStates extends Action\Action
{
    /**
     * @var JsonFactory
     */
    public $_resultJsonFactory;
    /**
     * @var StatesCollection
     */
    protected $_statesCollection;

    /**
     * GetTags constructor.
     * @param Action\Context $context
     * @param StatesCollection $statesCollection
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        StatesCollection $statesCollection,
        JsonFactory $resultJsonFactory
    ){
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_statesCollection = $statesCollection;
        return parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $collection = $this->_statesCollection->create()
                ->setOrder('states_name', 'ASC')
                ->getData();
            return (!empty($collection)) ? $result->setData(
                [
                    'request' => 'OK',
                    'result' => $collection]) : $result->setData(
                        [
                            'request' => 'No States!',
                            'result' => 'No States!'
                        ]
            );
        } else {
            return $result->setData(['request' => 'AJAX ERROR']);
        }
    }
}
