<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Block\Adminhtml\System;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $buttonBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');
        $store = $this->_request->getParam('store');
        if (!$store) {
            $store = null;
        }

        $params = [
            'store_key' => $this->_storeManager->getStore($store)->getCode()
        ];

        $initUrl = $this->getUrl('amasty_meta/meta/init', $params);
        $processUrl = $this->getUrl('amasty_meta/meta/process', $params);

        $updateParams = [
            'init_url' => $initUrl,
            'process_url' => $processUrl,
            'conclude_url' => $this->getUrl("amasty_meta/meta/done")
        ];

        $encodedParams = \Zend_Json::encode($updateParams);

        $data = [
            'label'     => __('Apply Template For Product URLs'),
            'id'        => 'ammeta_apply_templates',
            'class'     => '',
        ];

        $buttonBlock->setData($data);

        $applyBlock = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');

        $applyBlock
            ->setTemplate('Amasty_Meta::system/button/apply.phtml')
            ->setButton($buttonBlock)
            ->setConfig($encodedParams)
        ;

        return $applyBlock->toHtml();
    }
}
