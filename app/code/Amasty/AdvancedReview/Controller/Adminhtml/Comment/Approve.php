<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml\Comment;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Controller\Adminhtml\Comment as CommentController;
use Amasty\AdvancedReview\Model\Sources\CommentStatus;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Approve extends CommentController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            /** @var CommentInterface $model */
            $model = $this->getCommentRepository()->getById($id);
            $model->setStatus(CommentStatus::STATUS_APPROVED);
            $this->getCommentRepository()->save($model);
        } catch (LocalizedException $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getMessageManager()->addErrorMessage(__('Something went wrong'));
        }

        $this->getCommentNotification()->sendMessage($model);

        return $resultRedirect->setPath('*/*/');
    }
}
