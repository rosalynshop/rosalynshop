<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml\Reminder;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;

/**
 * Class MassStatus
 * @package Amasty\AdvancedReview\Controller\Adminhtml\Reminder
 */
class MassStatus extends \Amasty\AdvancedReview\Controller\Adminhtml\AbstractMassAction
{
    /**
     * @param ReminderInterface $reminder
     */
    protected function itemAction(ReminderInterface $reminder)
    {
        $reminder->setStatus($this->getRequest()->getParam('status'));
        $this->repository->save($reminder);
    }
}
