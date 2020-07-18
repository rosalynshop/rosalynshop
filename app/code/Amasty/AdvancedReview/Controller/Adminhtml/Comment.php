<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;

abstract class Comment extends Action
{
    const ADMIN_RESOURCE = 'Amasty_AdvancedReview::comments';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Amasty\AdvancedReview\Api\CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Amasty\AdvancedReview\Model\Email\CommentNotification
     */
    private $commentNotification;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Amasty\AdvancedReview\Api\CommentRepositoryInterface $commentRepository,
        \Psr\Log\LoggerInterface $logger,
        DataPersistorInterface $dataPersistor,
        \Amasty\AdvancedReview\Model\Email\CommentNotification $commentNotification
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->commentRepository = $commentRepository;
        $this->logger = $logger;
        $this->dataPersistor = $dataPersistor;
        $this->commentNotification = $commentNotification;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return DataPersistorInterface
     */
    public function getDataPersistor()
    {
        return $this->dataPersistor;
    }

    /**
     * @return \Amasty\AdvancedReview\Api\CommentRepositoryInterface
     */
    public function getCommentRepository()
    {
        return $this->commentRepository;
    }

    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getPageFactory()
    {
        return $this->resultPageFactory;
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
