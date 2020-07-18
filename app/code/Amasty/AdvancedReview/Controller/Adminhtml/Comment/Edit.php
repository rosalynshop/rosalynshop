<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml\Comment;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Controller\Adminhtml\Comment as CommentController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends CommentController
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($commentId = (int) $this->getRequest()->getParam(CommentInterface::ID)) {
            try {
                $comment = $this->getCommentRepository()->getById($commentId);
                /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
                $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
                $resultPage->getConfig()->getTitle()->prepend(__(
                    'Edit Comment #%1 by %2',
                    $comment->getId(),
                    $comment->getNickname()
                ));
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This comment no longer exists.'));

                return $this->_redirect('*/*/index');
            }
        } else {
            return $this->_redirect('*/*/index');
        }

        return $resultPage;
    }
}
