<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Reminder;

use Magento\Framework\App\Action\Context;
use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Model\EmailSender;
use Magento\Framework\Controller\ResultFactory;

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

    public function __construct(
        Context $context,
        Config $config,
        \Amasty\AdvancedReview\Model\UnsubscribeFactory $unsubscribeFactory,
        \Amasty\AdvancedReview\Model\Repository\UnsubscribeRepository $unsubscribeRepository
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->unsubscribeFactory = $unsubscribeFactory;
        $this->unsubscribeRepository = $unsubscribeRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        $hash = $this->getRequest()->getParam('hash');

        if ($email && $hash && $hash === $this->config->hash($email . EmailSender::SALT)) {
            try {
                $model = $this->unsubscribeFactory->create();
                $model->setEmail($email);
                $this->unsubscribeRepository->save($model);
            } catch (\Exception $exc) {
                $this->messageManager->addErrorMessage(
                    $this->getErrorMessage()
                );
            }
            $this->messageManager->addSuccessMessage(
                __('You unsubscribed.')
            );
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
     * @return \Magento\Framework\Phrase
     */
    private function getErrorMessage()
    {
        return __('Something went wrong while unsubscribing you.');
    }
}
