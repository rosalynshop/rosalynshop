<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class HolePunch extends AbstractFieldArray
{
    /**
     * Prepare field to render
     */
    protected function _prepareToRender()
    {
        $this->_addAfter = false;

        $this->addColumn(
            'template',
            [
                'label' => __('Template')
            ]
        );
        $this->addColumn(
            'block',
            [
                'label' => __('Block class')
            ]
        );
        $this->addColumn(
            'cms_block_id',
            [
                'label' => __('Cms block ID')
            ]
        );
    }
}
