<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Controller\Adminhtml\ImportCitiesList;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Upload
 * @package RosalynShop\RegionManager\Controller\Adminhtml\ImportCitiesList
 */
class Upload extends Action
{
    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * Upload constructor.
     * @param Action\Context $context
     * @param DirectoryList $directoryList
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Action\Context $context,
        DirectoryList $directoryList,
        JsonHelper $jsonHelper
    ){
        $this->_jsonHelper = $jsonHelper;
        $this->_directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try{
            $tmpDir = $this->_directoryList->getPath('tmp');
            $ext = pathinfo('import_cities_list.csv')['extension'];
            move_uploaded_file($this->getRequest()->getFiles("csv_uploader")['tmp_name'], $tmpDir . "/datasheet-citiesList." . $ext);
            return $this->jsonResponse(['error' => "File was successfully uploaded! You can import data."]);
        } catch (\Exception $e){
            return $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param string $response
     * @return mixed
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($response));
    }
}