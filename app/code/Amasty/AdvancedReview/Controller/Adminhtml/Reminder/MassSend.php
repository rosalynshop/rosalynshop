<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml\Reminder;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Api\ReminderRepositoryInterface;
use Amasty\AdvancedReview\Model\EmailSender;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Amasty\AdvancedReview\Model\ResourceModel\Reminder\CollectionFactory;
use Amasty\AdvancedReview\Model\ReminderFactory;

/**
 * Class MassSend
 * @package Amasty\AdvancedReview\Controller\Adminhtml\Reminder
 */
class MassSend extends \Amasty\AdvancedReview\Controller\Adminhtml\AbstractMassAction
{
    /**
     * @param ReminderInterface $reminder
     *
     * @throws LocalizedException
     */
    protected function itemAction(ReminderInterface $reminder)
    {
        $this->send($reminder, false, true);
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 email(s) have been sent.', $collectionSize);
        }

        return __('No records have been sent.');
    }
}
