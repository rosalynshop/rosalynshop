<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Ajax\Comment;

use Amasty\AdvancedReview\Api\CommentRepositoryInterface;
use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Block\Comment\Comment;
use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Model\Sources\CommentStatus;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    /**
     * @var null|Session
     */
    private $session;

    /**
     * @var \Amasty\AdvancedReview\Model\Email\CommentNotification
     */
    private $commentNotification;

    public function __construct(
        CommentRepositoryInterface $commentRepository,
        Config $config,
        SessionFactory $sessionFactory,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        Escaper $escaper,
        LoggerInterface $logger,
        Context $context,
        \Amasty\AdvancedReview\Model\Email\CommentNotification $commentNotification
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->formKeyValidator = $formKeyValidator;
        $this->commentRepository = $commentRepository;
        $this->config = $config;
        $this->escaper = $escaper;
        $this->storeManager = $storeManager;
        $this->sessionFactory = $sessionFactory;
        $this->commentNotification = $commentNotification;
    }

    public function execute()
    {
        $data = [
            'error' => $this->escaper->escapeHtml(__('Sorry. There is a problem with your Comment Request.'))
        ];
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if ($this->getRequest()->isAjax()) {
            try {
                $this->validateFormKey();
                $this->validateSession();
                if ($commentData = $this->getRequest()->getParam('comment')) {
                    $newComment = $this->commentRepository->save($this->getNewComment($commentData));
                    /** @var Comment $commentBlock */
                    $commentBlock = $this->_view->getLayout()->createBlock(Comment::class);
                    $isCommentApproved = $newComment->getStatus() == CommentStatus::STATUS_APPROVED;
                    if ($isCommentApproved) {
                        $this->commentNotification->sendMessage($newComment);
                        $commentBlock->setComment($newComment);
                    } else {
                        $commentBlock->setMessage(__('You submitted your review for moderation.'));
                    }
                    $comments = $this->commentRepository->getListByReviewId($commentData['review_id'])->getItems();
                    $data = [
                        'html' => $commentBlock->toHtml(),
                        'qty' => count($comments),
                        'approved' => $isCommentApproved
                    ];
                }
            } catch (LocalizedException $e) {
                $data['error'] = $e->getMessage();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $resultPage->setStatusHeader(
                \Zend\Http\Response::STATUS_CODE_403,
                \Zend\Http\AbstractMessage::VERSION_11,
                'Forbidden'
            );
            $data = [
                'error' =>__('Forbidden'),
                'errorcode' => 403
            ];
        }

        $resultPage->setData($data);

        return $resultPage;
    }

    /**
     * @throws LocalizedException
     */
    private function validateFormKey()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(
                __('Form key is not valid. Please try to reload the page.')
            );
        }
    }

    /**
     * @throws LocalizedException
     */
    private function validateSession()
    {
        if (!$this->getSession()->isLoggedIn() && !$this->config->isGuestCanComment()) {
            throw new LocalizedException(
                __('Your session was expired. Please refresh this page and try again.')
            );
        }
    }

    /**
     * @param array $commentData
     * @return CommentInterface
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getNewComment($commentData)
    {
        $this->validateData($commentData);
        $commentData[CommentInterface::MESSAGE] = $this->prepareComment($commentData[CommentInterface::MESSAGE] ?? '');
        $newComment = $this->commentRepository->getComment();
        $newComment->addData($commentData);
        if ($this->config->isCommentApproved()) {
            $newComment->setStatus(CommentStatus::STATUS_APPROVED);
        } else {
            $newComment->setStatus(CommentStatus::STATUS_PENDING);
        }
        if ($this->getSession()->isLoggedIn()) {
            $newComment->setEmail($this->getSession()->getCustomer()->getEmail());
            $newComment->setCustomerId($this->getSession()->getCustomerId());
            $newComment->setNickname($this->getSession()->getCustomer()->getName());
        }
        $newComment->setSessionId($this->getSession()->getSessionId());
        $newComment->setStoreId($this->storeManager->getStore()->getId());

        return $newComment;
    }

    /**
     * @param $commentData
     * @throws LocalizedException
     */
    private function validateData($commentData)
    {
        foreach ($commentData as $field => $value) {
            if (empty($value)) {
                throw new LocalizedException(
                    __('%1 is a required field.', ucfirst($field))
                );
            }
        }
    }

    /**
     * @param $message
     * @return string
     */
    private function prepareComment($message)
    {
        $message = htmlspecialchars_decode($message);
        $message = strip_tags($message);
        $message = trim($message);

        return $message;
    }

    /**
     * @return Session
     */
    private function getSession()
    {
        if ($this->session === null) {
            $this->session = $this->sessionFactory->create();
        }

        return $this->session;
    }
}
