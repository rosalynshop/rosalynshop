<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Amasty\AdvancedReview\Model\ResourceModel\Reminder\CollectionFactory;
use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Model\SendReminderEmails;
use Amasty\AdvancedReview\Api\ReminderRepositoryInterface;

/**
 * Class AbstractMassAction
 * @package Amasty\AdvancedReview\Controller\Adminhtml
 */
abstract class AbstractMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_AdvancedReview::reminder';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $reminderCollectionFactory;

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    protected $config;

    /**
     * @var SendReminderEmails
     */
    private $sendReminderEmails;

    /**
     * @var ReminderRepositoryInterface
     */
    protected $repository;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        CollectionFactory $reminderCollectionFactory,
        \Amasty\AdvancedReview\Helper\Config $config,
        SendReminderEmails $sendReminderEmails,
        ReminderRepositoryInterface $repository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->reminderCollectionFactory = $reminderCollectionFactory;
        $this->config = $config;
        $this->sendReminderEmails = $sendReminderEmails;
        $this->repository = $repository;
    }

    /**
     * Execute action for reminder
     *
     * @param ReminderInterface $reminder
     */
    abstract protected function itemAction(ReminderInterface $reminder);

    /**
     * @param ReminderInterface $reminder
     * @param bool $isTestEmail
     * @param bool $force
     */
    public function send(ReminderInterface $reminder, $isTestEmail = false, $force = false)
    {
        $this->sendReminderEmails->send($reminder, $isTestEmail);
    }

    /**
     * Mass action execution
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider(); // compatibility with Mass Actions on Magento 2.1.0
        /** @var \Amasty\AdvancedReview\Model\ResourceModel\Reminder\Collection $collection */
        $collection = $this->filter->getCollection($this->reminderCollectionFactory->create());

        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                foreach ($collection->getItems() as $reminder) {
                    $this->itemAction($reminder);
                }

                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been changed.', $collectionSize);
        }

        return __('No records have been changed.');
    }
}
