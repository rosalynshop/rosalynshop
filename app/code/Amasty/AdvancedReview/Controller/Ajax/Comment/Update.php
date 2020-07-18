<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Ajax\Comment;

use Amasty\AdvancedReview\Block\Comment\Container as CommentsBlock;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Update extends \Magento\Framework\App\Action\Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        Context $context
    ) {
        parent::__construct($context);
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = [
            'error' => '',
            'items' => []
        ];
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if ($this->getRequest()->isAjax()) {
            try {
                $this->_view->loadLayout();
                $data['items'] = $this->generateItemsHtml();
                unset($data['error']);
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
     * @return array
     */
    private function generateItemsHtml()
    {
        /** @var CommentsBlock $commentsBlock */
        $commentsBlock = $this->_view->getLayout()->getBlock('review.comments');
        $reviewIds = $this->getRequest()->getParam('review_ids', []);
        $items = [];
        foreach ($reviewIds as $reviewId) {
            $items[$reviewId]['html'] = $commentsBlock->setReviewId($reviewId)->toHtml();
            $items[$reviewId]['count'] = count($commentsBlock->getComments());
        }

        return $items;
    }
}
