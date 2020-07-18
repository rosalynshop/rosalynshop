<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Comment;

use Amasty\AdvancedReview\Helper\Config;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;

class Form extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::comments/form.phtml';

    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    /**
     * @var null|Session
     */
    private $session;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        SessionFactory $sessionFactory,
        Config $config,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sessionFactory = $sessionFactory;
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function getReviewId()
    {
        return $this->getParentBlock()->getReviewId();
    }

    /**
     * @inheritdoc
     */
    public function _toHtml()
    {
        $html = '';

        if ($this->canUserComment()) {
            $html = parent::_toHtml();
        }

        return $html;
    }

    /**
     * @return bool
     */
    public function canUserComment()
    {
        return $this->isLoggedIn() || $this->config->isGuestCanComment();
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        if ($this->session === null) {
            $this->session = $this->sessionFactory->create();
        }

        return $this->session;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->getSession()->isLoggedIn();
    }
}
