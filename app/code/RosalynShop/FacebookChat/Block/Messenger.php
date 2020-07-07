<?php
/**
 * @author   Rosalynshop <info@rosalynshop.com>
 * @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\FacebookChat\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use RosalynShop\FacebookChat\Helper\Data;

/**
 * Class Messenger
 * @package RosalynShop\FacebookChat\Block
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
