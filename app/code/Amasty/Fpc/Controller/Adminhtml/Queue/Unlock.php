<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Controller\Adminhtml\Queue;

use Amasty\Fpc\Model\Queue;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Unlock extends \Amasty\Fpc\Controller\Adminhtml\Queue
{
    /**
     * @var Queue
     */
    private $queue;

    public function __construct(
        Context $context,
        Queue $queue
    ) {
        parent::__construct($context);
        $this->queue = $queue;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $this->queue->forceUnlock();
        $this->messageManager->addSuccessMessage(__('Unlocked successfully!'));

        return $this->_redirect('*/*/');
    }
}
