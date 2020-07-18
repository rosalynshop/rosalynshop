<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Comment;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\View\Element\Template;

class Comment extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::comments/comment.phtml';

    /**
     * @var CommentInterface
     */
    private $comment;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    public function __construct(
        DateTimeFactory $dateTimeFactory,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * @return CommentInterface
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param CommentInterface $comment
     * @return $this
     */
    public function setComment(CommentInterface $comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param string $message
     */
    public function setMessage($message) {
        $this->setTemplate('Amasty_AdvancedReview::comments/message.phtml');
        $this->setData('message', $message);
    }

    /**
     * @return string
     */
    public function getDateInterval()
    {
        $commentCreatedDate = $this->dateTimeFactory->create(
            $this->getComment()->getCreatedAt(),
            new \DateTimeZone('UTC')
        );
        $currentDate = $this->dateTimeFactory->create('now', new \DateTimeZone('UTC'));
        $interval = $commentCreatedDate->diff($currentDate);
        switch (true) {
            case $interval->y > 0:
                $intervalMessage = __('%1 year(s) ago', $interval->y);
                break;
            case $interval->m > 0:
                $intervalMessage = __('%1 month(s) ago', $interval->m);
                break;
            case $interval->d > 0:
                $intervalMessage = __('%1 day(s) ago', $interval->d);
                break;
            case $interval->h > 0:
                $intervalMessage = __('%1 hour(s) ago', $interval->h);
                break;
            case $interval->i > 0:
                $intervalMessage = __('%1 minute(s) ago', $interval->i);
                break;
            case $interval->s > 0:
                $intervalMessage = __('%1 second(s) ago', $interval->s);
                break;
            default:
                $intervalMessage = __('recently');
        }

        return $intervalMessage;
    }
}
