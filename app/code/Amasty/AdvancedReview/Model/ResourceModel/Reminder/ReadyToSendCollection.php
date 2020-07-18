<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Reminder;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Model\OptionSource\Reminder\Status;

/**
 * Class ReadyToSendCollection
 * @package Amasty\AdvancedReview\Model\ResourceModel\Reminder
 */
class ReadyToSendCollection extends Collection
{
    /**
     * @return $this
     */
    public function execute()
    {
        $this->addFieldToFilter(ReminderInterface::STATUS, Status::WAITING);
        $this->getSelect()->where(ReminderInterface::SEND_DATE . '< NOW()');

        return $this;
    }
}
