<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Vote
 * @package Amasty\AdvancedReview\Controller\Ajax
 * phpcs:ignoreFile
 */
class Vote extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Amasty\AdvancedReview\Model\Repository\VoteRepository
     */
    private $voteRepository;

    /**
     * @var \Amasty\AdvancedReview\Model\VoteFactory
     */
    private $voteFactory;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Amasty\AdvancedReview\Model\VoteFactory $voteFactory,
        \Amasty\AdvancedReview\Model\Repository\VoteRepository $voteRepository,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->logger = $logger;
        $this->jsonEncoder = $jsonEncoder;
        $this->remoteAddress = $remoteAddress;
        $this->voteRepository = $voteRepository;
        $this->voteFactory = $voteFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $message = [
            'error' => __('Sorry. There is a problem with your Vote Request.')
        ];

        if ($this->getRequest()->isAjax()) {
            try {
                if (!$this->formKeyValidator->validate($this->getRequest())) {
                    throw new LocalizedException(
                        __('Form key is not valid. Please try to reload the page.')
                    );
                }

                $type = $this->getRequest()->getParam('type');
                $reviewId = (int)$this->getRequest()->getParam('review');
                if ($reviewId > 0 && in_array($type, ['plus', 'minus', 'update'])) {
                    $ip = $this->remoteAddress->getRemoteAddress();

                    if ($type != 'update') {
                        $type = ($type == 'plus') ? '1' : '0';

                        /** @var  \Amasty\AdvancedReview\Model\Vote $model */
                        $model = $this->voteRepository->getByIdAndIp($reviewId, $ip);
                        $modelType = $model->getType();
                        if ($model->getVoteId()) {
                            $this->voteRepository->delete($model);
                        }

                        if ($modelType === null || $modelType != $type) {
                            $model = $this->voteFactory->create();
                            $model->setIp($ip);
                            $model->setReviewId($reviewId);
                            $model->setType($type);
                            $this->voteRepository->save($model);
                        }
                    }

                    $votesForReview = $this->voteRepository->getVotesCount($reviewId);
                    $voted = $this->voteRepository->getVotesCount($reviewId, $ip);
                    $message = [
                        'success' => __('Success.'),
                        'data' => $votesForReview,
                        'voted' => $voted
                    ];
                }
            } catch (LocalizedException $e) {
                $message = ['error' => $e->getMessage()];
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        $resultPage = $this->resultJsonFactory->create();
        $resultPage->setHttpResponseCode(200);
        $resultPage->setData($message);
        return $resultPage;
    }
}
