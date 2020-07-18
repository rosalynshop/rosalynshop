<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Adminhtml\System\Config\Field;

use Magento\Framework\Data\Form\Element\AbstractElement as AbstractElement;

class Multiselect extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setData('size', count($element->getValues()) ?: 10);
        return $element->getElementHtml();
    }
}
