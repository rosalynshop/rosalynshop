<?php

/**
 * @author   Rosalynshop <info@rosalynshop.com>
 * @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Controller\Adminhtml\Wards;

use Magento\Framework\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use RosalynShop\RegionManager\Helper\Data as HelperData;
use RosalynShop\RegionManager\Model\ResourceModel\Cities\CollectionFactory as CitiesCollection;

/**
 * Class WardsJs
 * @package RosalynShop\RegionManager\Controller\Adminhtml\Wards
 */
class WardsJs extends Action\Action
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var CitiesCollection
     */
    protected $_citiesCollection;

    /**
     * Login constructor.
     * @param Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $resultJsonFactory,
        HelperData $helperData,
        CitiesCollection $citiesCollection
    ){
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->helperData = $helperData;
        $this->_citiesCollection = $citiesCollection;
        return parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $resultJson = $this->_resultJsonFactory->create();
        $isAjax = $this->getRequest()->getParam('isAjax');
        $response = [
            'request' => 'AJAX ERROR'
        ];
        if ($isAjax) {
            $post = $this->getRequest()->getParam('selected_state');
            $collection = $this->_citiesCollection->create()
                ->addFieldToFilter('states_name', $post)
                ->setOrder('cities_name', 'ASC')
                ->getData();

            if (!empty($collection)) {
                $response = [
                    'request' => 'OK',
                    'result' => $collection
                ];
            } else {
                $response = [
                    'request' => 'NULL',
                    'result' => 'Không có quận huyện nào!'
                ];
            }
        }
        return $resultJson->setData($response);
    }
}
