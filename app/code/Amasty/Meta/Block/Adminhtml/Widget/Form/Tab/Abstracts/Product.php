<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Block\Adminhtml\Widget\Form\Tab\Abstracts;

class Product extends Tab
{
    protected function _addFieldsToFieldset($fieldSet)
    {
        $fieldSet->addField(
            $this->_prefix . 'product_meta_title',
            'text',
            array(
                'label' => __('Title'),
                'name'  => $this->_prefix . 'product_meta_title',
                'note'  => __('
Example: Buy {name} [by {manufacturer|brand}] [of {color} color] [for only {price}] [in {categories}] at [{store},] {website}.    
<br/>                            
<br/>Available variables:  
<br/>Category - {category}
<br/>All Categories - {categories}
<br/>Store View - {store_view}
<br/>Store      - {store}
<br/>Website    - {website}
<br/>Price - {price}
<br/>Special Price - {special_price}
<br/>Final Price - {final_price}
<br/>Final Price with Tax - {final_price_incl_tax}
<br/>Price From (bundle) - {startingfrom_price}
<br/>Price To (bundle) - {startingto_price}
<br/>Brand - {brand}
<br/>Color - {color}
<br/>And other product attributes ...'),
            )
        );

        $fieldSet->addField(
            $this->_prefix . 'product_meta_description',
            'textarea',
            array(
                'label' => __('Meta Description'),
                'name'  => $this->_prefix . 'product_meta_description'
            )
        );

        $fieldSet->addField(
            $this->_prefix . 'product_meta_keywords',
            'textarea',
            array(
                'label' => __('Keywords'),
                'name'  => $this->_prefix . 'product_meta_keywords'
            )
        );

        $fieldSet->addField(
            $this->_prefix . 'product_h1_tag',
            'text',
            array(
                'label' => __('H1 Tag'),
                'name'  => $this->_prefix . 'product_h1_tag'
            )
        );

        $fieldSet->addField(
            $this->_prefix . 'product_short_description',
            'editor',
            array(
                'label'   => __('Short Description'),
                'name'    => $this->_prefix . 'product_short_description',
                'style'   => 'width:700px; height:200px;',
                'wysiwyg' => true,

            )
        );

        $fieldSet->addField(
            $this->_prefix . 'product_description',
            'editor',
            array(
                'label'   => __('Description'),
                'name'    => $this->_prefix . 'product_description',
                'style'   => 'width:700px; height:200px;',
                'wysiwyg' => true,

            )
        );
    }

}
