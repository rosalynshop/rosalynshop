<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml\Comment;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

abstract class AbstractMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_AdvancedReview::comments';

    /**
     * @var \Amasty\AdvancedReview\Model\Repository\CommentRepository
     */
    private $repository;

    /**
     * @var \Amasty\AdvancedReview\Model\ResourceModel\Comment\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\AdvancedReview\Model\Email\CommentNotification
     */
    private $commentNotification;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        \Amasty\AdvancedReview\Model\Repository\CommentRepository $repository,
        \Amasty\AdvancedReview\Model\ResourceModel\Comment\CollectionFactory $collectionFactory,
        \Amasty\AdvancedReview\Model\Email\CommentNotification $commentNotification
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->logger = $logger;
        $this->commentNotification = $commentNotification;
    }

    /**
     * Execute action for category
     *
     * @param $category
     */
    abstract protected function itemAction($category);

    /**
     * @return \Amasty\AdvancedReview\Model\Repository\CommentRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Mass action execution
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider(); // compatibility with Mass Actions on Magento 2.1.0

        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                foreach ($collection->getItems() as $model) {
                    $this->itemAction($model);
                }
                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
            } catch (LocalizedException $e) {
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
        return $collectionSize
            ? __('A total of %1 record(s) have been changed.', $collectionSize)
            : __('No records have been changed.');
    }

    /**
     * @return \Amasty\AdvancedReview\Model\Email\CommentNotification
     */
    public function getCommentNotification()
    {
        return $this->commentNotification;
    }

    /**
     * @param \Amasty\AdvancedReview\Model\Email\CommentNotification $commentNotification
     */
    public function setCommentNotification($commentNotification)
    {
        $this->commentNotification = $commentNotification;
    }
}
