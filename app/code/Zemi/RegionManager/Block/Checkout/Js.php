<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Zemi\RegionManager\Model\Config;

/**
 * Class Js
 * @package Zemi\RegionManager\Block\Checkout
 */
class Js extends Template
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * Js constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = []
    ) {
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function enableModule()
    {
        return $this->_config->getEnableExtensionYesNo() == 1 ? true : false;
    }
}
