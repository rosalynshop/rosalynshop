<?php

/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RegionManager\Block\Adminhtml\Wards;

use Magento\Backend\Block\Template;
use Zemi\RegionManager\Helper\Data as HelperData;

/**
 * Class WardsJs
 * @package Zemi\RegionManager\Block\Adminhtml\Wards
 */
class WardsJs extends Template
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * WardsJs constructor.
     * @param Template\Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getWardsAjaxUrl()
    {
        return $this->helperData->getWardsAjaxUrl();
    }
}
