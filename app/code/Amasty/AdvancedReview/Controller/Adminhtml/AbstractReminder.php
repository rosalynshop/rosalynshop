<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class AbstractReminder
 * @package Amasty\AdvancedReview\Controller\Adminhtml
 */
abstract class AbstractReminder extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_AdvancedReview::reminder';
}
