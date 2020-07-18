<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml\Comment;

use Amasty\AdvancedReview\Api\Data\CommentInterface;

class MassDelete extends AbstractMassAction
{
    /**
     * @param CommentInterface $comment
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function itemAction($comment)
    {
        try {
            $this->getRepository()->deleteById($comment->getId());
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        return $collectionSize
            ? __('A total of %1 record(s) have been deleted.', $collectionSize)
            : __('No records have been deleted.');
    }
}
