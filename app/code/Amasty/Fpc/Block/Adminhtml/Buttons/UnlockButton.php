<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Buttons;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class UnlockButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $alertMessage = __('Are you sure you want to do this?');
        $onClick = sprintf('confirmSetLocation("%s", "%s")', $alertMessage, $this->getUnlockUrl());

        return [
            'label' => __('Force Unlock'),
            'class' => 'unlock',
            'on_click' => $onClick,
            'sort_order' => 30,
        ];
    }

    /**
     * @return string
     */
    public function getUnlockUrl()
    {
        return $this->urlBuilder->getUrl('*/*/unlock');
    }
}
