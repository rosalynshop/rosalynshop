<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Controller\Adminhtml\ImportCitiesList;

use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use RosalynShop\RegionManager\Model\Cities;
use RosalynShop\RegionManager\Model\ResourceModel\Cities\CollectionFactory;

/**
 * Class Import
 * @package RosalynShop\RegionManager\Controller\Adminhtml\ImportCitiesList
 */
class Import extends Action
{
    /**
     * @var Csv
     */
    protected $_csv;
    /**
     * @var Cities
     */
    protected $_citiesListModel;
    /**
     * @var CollectionFactory
     */
    protected $_citiesListCollection;
    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * Import constructor.
     * @param Action\Context $context
     * @param Csv $csv
     * @param Cities $citiesListModel
     * @param DirectoryList $directoryList
     * @param CollectionFactory $citiesListCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        Csv $csv,
        Cities $citiesListModel,
        DirectoryList $directoryList,
        CollectionFactory $citiesListCollectionFactory
    ) {
        $this->_csv = $csv;
        $this->_citiesListModel = $citiesListModel;
        $this->_citiesListCollection = $citiesListCollectionFactory;
        $this->_directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws Exception
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $cities_list = $this->_citiesListModel;

        /**
         * Get Cities List and filter only email
         */
        $cities_list_collection = $this->_citiesListCollection->create();
        $data = $cities_list_collection->getData();
        $cities_list_city = [];

        foreach ($data as $item) {
            $cities_list_city[] = $item['cities_name'];
        }

        $tmpDir = $this->_directoryList->getPath('tmp');
        $file = $tmpDir . '/datasheet-citiesList.csv';

        if (!isset($file)) {
            throw new LocalizedException(__('Invalid file upload attempt.'));
        }

        $csv = $this->_csv;
        $csv->setDelimiter(',');
        $csvData = $csv->getData($file);

        foreach ($csvData as $row => $data) {
            if (count($data) == 2) {
                if ($data[0] == 'State name') {
                    continue;
                }

                if (!in_array($data[1], $cities_list_city)) {
                    $cities_list->setData([
                        'states_name' => trim($data[0], ' '),
                        'cities_name' => trim($data[1], ' ')
                    ])->save();
                }
            } else {
                $this->messageManager->addError('The list of states should be in two column!');
                return $resultRedirect->setPath('*/*/index');
            }
        }
        $this->messageManager->addSuccess(count($csvData) . ' imported ');
        return $resultRedirect->setPath('regionmanager/cities/index');
    }
}
