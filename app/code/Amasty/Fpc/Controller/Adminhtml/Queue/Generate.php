<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Controller\Adminhtml\Queue;

use Amasty\Fpc\Model\Queue;
use Magento\Backend\App\Action\Context;

class Generate extends \Amasty\Fpc\Controller\Adminhtml\Queue
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

    public function execute()
    {
        try {
            list($result, $processedItems) = $this->queue->generate();

            if ($result) {
                $this->messageManager->addSuccessMessage(
                    __('Warmer queue has been successfully generated for %1 URLs.', $processedItems)
                );
            } else {
                $this->messageManager->addWarningMessage(__('Warmer queue was disturbed by another process'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/*/');
    }
}
