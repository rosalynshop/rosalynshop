<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Controller\Adminhtml\Flushes;

use Amasty\Fpc\Model\ResourceModel\FlushesLog;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Clear extends Action
{
    /**
     * @var FlushesLog
     */
    private $flushesLogResource;

    public function __construct(
        Context $context,
        FlushesLog $flushesLogResource
    ) {
        parent::__construct($context);
        $this->flushesLogResource = $flushesLogResource;
    }

    public function execute()
    {
        try {
            $this->flushesLogResource->truncateTable();

            $this->messageManager->addSuccessMessage(__('Cache flushes log has been successfully cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Fpc::clear_flushes_log');
    }
}
