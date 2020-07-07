<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Controller\Adminhtml\ImportStatesList;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Upload extends Action
{
    protected $_directoryList;
    protected $_jsonHelper;

    public function __construct(
        Action\Context $context,
        DirectoryList $directoryList,
        JsonHelper $jsonHelper
    ) {
        $this->_jsonHelper = $jsonHelper;
        $this->_directoryList = $directoryList;
        parent::__construct($context);
    }

    public function execute()
    {
        try{
            $tmpDir = $this->_directoryList->getPath('tmp');
            $ext = pathinfo('import_states_list.csv')['extension'];
            move_uploaded_file($this->getRequest()->getFiles("csv_uploader")['tmp_name'], $tmpDir . "/datasheet-statesList." . $ext);
            return $this->jsonResponse(['error' => "File was successfully uploaded! You can import data."]);
        }catch (\Exception $e){
            return $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($response));
    }

}