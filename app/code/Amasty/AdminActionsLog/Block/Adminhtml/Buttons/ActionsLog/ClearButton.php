<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\Buttons\ActionsLog;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Backend\Block\Widget\Context;

class ClearButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
    }

    public function getButtonData()
    {
        $data = [
            'label' => __('Clear Log'),
            'class' => 'delete primary',
            'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->urlBuilder->getUrl('*/*/clear') . '\')',
            'sort_order' => 20,
        ];

        return $data;
    }
}
