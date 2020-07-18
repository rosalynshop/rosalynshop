<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Comment;

use Amasty\AdvancedReview\Api\CommentRepositoryInterface;
use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Model\Comment;
use Magento\Framework\View\Element\Template;

class Container extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::comments/container.phtml';

    /**
     * @var null|int
     */
    private $reviewId = null;

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var Comment[]
     */
    private $comments;

    public function __construct(
        CommentRepositoryInterface $commentRepository,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->commentRepository = $commentRepository;
    }

    /**
     * @return int|null
     */
    public function getReviewId()
    {
        return $this->reviewId;
    }

    /**
     * @param int $review
     * @return $this
     */
    public function setReviewId($reviewId)
    {
        $this->reviewId = $reviewId;
        return $this;
    }

    /**
     * @return CommentInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getComments()
    {
        $this->comments = $this->commentRepository->getListByReviewId($this->getReviewId())
            ->getItems();

        return $this->comments;
    }
}
