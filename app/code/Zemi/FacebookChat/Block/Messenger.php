<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\FacebookChat\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Zemi\FacebookChat\Helper\Data;

/**
 * Class Messenger
 * @package Zemi\FacebookChat\Block
 */
class Messenger extends Template
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Messenger constructor.
     * @param Context $context
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Data $helperData
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
    }

    /**
     * @return mixed
     */
    public function isEnabledFacebookChat()
    {
        return $this->helperData->isEnable();
    }

    /**
     * @return mixed
     */
    public function getFacebookPageId()
    {
        return $this->helperData->getFBPageId();
    }

    /**
     * @return mixed
     */
    public function getFacebookColor()
    {
        return $this->helperData->getFBColor();
    }
}
