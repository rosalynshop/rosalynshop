<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml\Reminder;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Model\OptionSource\Reminder\Status;

/**
 * Class MassCancel
 * @package Amasty\AdvancedReview\Controller\Adminhtml\Reminder
 */
class MassCancel extends \Amasty\AdvancedReview\Controller\Adminhtml\AbstractMassAction
{
    /**
     * @param ReminderInterface $reminder
     */
    protected function itemAction(ReminderInterface $reminder)
    {
        $reminder->setStatus(Status::CANCELED);
        $this->repository->save($reminder);
    }
}
