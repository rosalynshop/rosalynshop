<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Controller\Adminhtml\Activity;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Amasty\Fpc\Model\ResourceModel\Activity;

class Clear extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_Fpc::clear_activity_log';

    /**
     * @var Activity
     */
    private $activityResource;

    public function __construct(
        Context $context,
        Activity $activityResource
    ) {
        parent::__construct($context);
        $this->activityResource = $activityResource;
    }

    public function execute()
    {
        try {
            $this->activityResource->truncate();

            $this->messageManager->addSuccessMessage(__('Activity log has been successfully cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/*/');
    }
}
