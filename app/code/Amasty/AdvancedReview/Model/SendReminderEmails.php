<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Api\ReminderRepositoryInterface;
use Amasty\AdvancedReview\Model\OptionSource\Reminder\Status;
use Amasty\AdvancedReview\Model\ResourceModel\Reminder\ReadyToSendCollectionFactory;
use Amasty\AdvancedReview\Model\Repository\ReminderRepository;
use Psr\Log\LoggerInterface;

/**
 * Class SendReminderEmails
 * @package Amasty\AdvancedReview\Model
 */
class SendReminderEmails
{
    /**
     * @var ReminderRepository
     */
    private $reminderRepository;

    /**
     * @var ReadyToSendCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $data;

    /**
     * @var ReminderRepositoryInterface
     */
    private $repository;

    /**
     * @var EmailSender
     */
    private $emailSender;

    public function __construct(
        ReminderRepository $reminderRepository,
        ReadyToSendCollectionFactory $collectionFactory,
        LoggerInterface $logger,
        ReminderRepositoryInterface $repository,
        EmailSender $emailSender,
        $data = []
    ) {
        $this->reminderRepository = $reminderRepository;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        $this->data = $data;
        $this->repository = $repository;
        $this->emailSender = $emailSender;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        foreach ($this->collectionFactory->create()->execute() as $item) {
            try {
                $this->send($item);
            } catch (\Exception $exc) {
                $this->logger->critical($exc);
            }
        }
    }

    /**
     * @param ReminderInterface $reminder
     * @param bool $isTestEmail
     * @param bool $force
     */
    public function send(ReminderInterface $reminder, $isTestEmail = false, $force = false)
    {
        try {
            $status = $this->emailSender->send($reminder, $isTestEmail, $force);

            if (!$isTestEmail) {
                $reminder->setSendDate(time())
                    ->setStatus($status);
                $this->repository->save($reminder);
            }

            return true;
        } catch (\Exception $exc) {
            $this->logger->critical($exc);
            if (!$isTestEmail) {
                $reminder->setStatus(Status::FAILED);
                $this->repository->save($reminder);
            }
        }

        return false;
    }
}
