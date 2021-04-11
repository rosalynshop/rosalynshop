<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Controller\Ajax;

use Magento\Framework\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Zemi\RegionManager\Model\Config;
use Zemi\RegionManager\Model\ResourceModel\Cities\CollectionFactory as CitiesCollection;

/**
 * Class GetCities
 * @package Zemi\RegionManager\Controller\Ajax
 */
class GetCities extends Action\Action
{
    /**
     * @var JsonFactory
     */
    public $_resultJsonFactory;

    /**
     * @var CitiesCollection
     */
    protected $_citiesCollection;

    /**
     * GetTags constructor.
     * @param Action\Context $context
     * @param CitiesCollection $citiesCollection
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        CitiesCollection $citiesCollection,
        JsonFactory $resultJsonFactory
    ){
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_citiesCollection = $citiesCollection;
        return parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $post = $this->getRequest()->getParam('selected_state');
            $collection = $this->_citiesCollection->create()
                ->addFieldToFilter('states_name', $post)
                ->setOrder('cities_name', 'ASC')
                ->getData();
            return (!empty($collection)) ? $result->setData([
                    'request' => 'OK',
                    'result' => $collection]) : $result->setData(
                        [
                            'request' => 'Không có địa chỉ!',
                            'result' => 'Không có địa chỉ!'
                        ]
            );
        } else {
            return $result->setData(['request' => 'AJAX ERROR']);
        }
    }
}
