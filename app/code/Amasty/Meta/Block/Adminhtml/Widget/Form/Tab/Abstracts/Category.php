<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Block\Adminhtml\Widget\Form\Tab\Abstracts;

class Category extends Tab
{
    protected function _addFieldsToFieldset($fieldSet)
    {

        $fieldSet->addField(
            $this->_prefix . 'cat_meta_title',
            'text',
            array(
                'label' => __('Title'),
                'name'  => $this->_prefix . 'cat_meta_title',
                'note'  => __('Available variables:  <br/>Parent Category - {meta_parent_category}<br/>Category Name - {name}<br/>Store View - {store_view}<br/>Store - {store}<br/>Website - {website}'),
            )
        );

        $fieldSet->addField(
            $this->_prefix . 'cat_meta_description',
            'textarea',
            array(
                'label' => __('Meta Description'),
                'name'  => $this->_prefix . 'cat_meta_description'
            )
        );

        $fieldSet->addField(
            $this->_prefix . 'cat_meta_keywords',
            'textarea',
            array(
                'label' => __('Keywords'),
                'name'  => $this->_prefix . 'cat_meta_keywords'
            )
        );

        $fieldSet->addField(
            $this->_prefix . 'cat_h1_tag',
            'text',
            array(
                'label' => __('H1 Tag'),
                'name'  => $this->_prefix . 'cat_h1_tag'
            )
        );

        $fieldSet->addField(
            $this->_prefix . 'cat_description',
            'textarea',
            array(
                'label' => __('Description'),
                'name'  => $this->_prefix . 'cat_description',
            )
        );

        $fieldSet->addField(
            $this->_prefix . 'cat_image_alt',
            'text',
            array(
                'label' => __('Image Alt'),
                'name'  => $this->_prefix . 'cat_image_alt',
                'note'    => __('Please, make sure that category image is wrapped into tag with class \'category-image\' and image has %s attribute', 'alt')
            )
        );

        //temporary disabled
        /*
        $fieldSet->addField(
            $this->_prefix . 'cat_image_title',
            'text',
            array(
                'label' => __('Image Title'),
                'name'  => $this->_prefix . 'cat_image_title',
                'note'    => __('Please, make sure that category image is wrapped into tag with class \'category-image\' and image has %s attribute', 'title')
            )
        );
*/
        $fieldSet->addField(
            $this->_prefix . 'cat_after_product_text',
            'textarea',
            array(
                'label' => __('Text after Product List'),
                'name'  => $this->_prefix . 'cat_after_product_text',
                'note'  => __('Current text always appears after products block')
            )
        );

    }
}
