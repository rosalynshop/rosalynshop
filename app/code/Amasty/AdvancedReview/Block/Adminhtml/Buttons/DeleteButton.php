<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Adminhtml\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $id = $this->request->getParam('id');
        if ($id) {
            $deleteUrl = $this->urlBuilder->getUrl('*/*/delete', ['id' => $id]);
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . $this->getConfirmText()
                    . '\', \'' . $deleteUrl . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getConfirmText()
    {
        return __('Are you sure you want to delete this?');
    }
}
