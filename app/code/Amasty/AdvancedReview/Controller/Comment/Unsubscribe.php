<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Comment;

use Magento\Framework\App\Action\Context;
use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Model\Email\CommentNotification;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Unsubscribe
 * @package Amasty\AdvancedReview\Controller\Reminder
 */
class Unsubscribe extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Amasty\AdvancedReview\Model\UnsubscribeFactory
     */
    private $unsubscribeFactory;

    /**
     * @var \Amasty\AdvancedReview\Model\Repository\UnsubscribeRepository
     */
    private $unsubscribeRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        Config $config,
        \Amasty\AdvancedReview\Model\UnsubscribeFactory $unsubscribeFactory,
        \Amasty\AdvancedReview\Model\Repository\UnsubscribeRepository $unsubscribeRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->unsubscribeFactory = $unsubscribeFactory;
        $this->unsubscribeRepository = $unsubscribeRepository;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        $hash = $this->getRequest()->getParam('hash');

        if ($email && $hash && $this->isHashValid($email, $hash)) {
            $this->unsubscribe($email);
        } else {
            $this->messageManager->addErrorMessage(
                $this->getErrorMessage()
            );
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('/');

        return $resultRedirect;
    }

    /**
     * @param string $email
     * @param string $hash
     * @return bool
     */
    private function isHashValid($email = '', $hash = '')
    {
        return $hash === $this->config->hash($email . CommentNotification::SALT);
    }

    /**
     * @param string $email
     */
    private function unsubscribe($email = '')
    {
        try {
            $model = $this->unsubscribeFactory->create();
            $model->setEmail($email);
            $model->setIsComment(true);
            $this->unsubscribeRepository->save($model);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->messageManager->addErrorMessage(
                $this->getErrorMessage()
            );
        }
        $this->messageManager->addSuccessMessage(
            __('You have been unsubscribed from comment messages.')
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    private function getErrorMessage()
    {
        return __('Something went wrong while unsubscribing you.');
    }
}
