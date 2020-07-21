<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Block\Adminhtml\Widget\Grid\Column\Filter;
class Store extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Store
{

    /**
     * Render HTML of the element
     *
     * @return string
     */
    public function getHtml()
    {
        $columnValue = $this->getColumn()->getValue();
        $addToHtml = '<option value="0" ' . ($columnValue === 0 ? ' selected="selected"' : '') . '>' .
                     __('Default')
                     . '</option>';

        $html = parent::getHtml();

        return preg_replace('/^(\<select.+?\<\/option\>)/', '$1' . $addToHtml, $html);
    }

}
