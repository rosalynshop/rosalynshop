<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Block\Adminhtml\Config\Edit;
use Magento\TestFramework\Event\Magento;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('configTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Template Configuration'));
    }

    protected function _beforeToHtml()
    {
        $name = __('Main Category');
        $this->addTab('general', array(
                'label'   => $name,
                'content' => $this->getLayout()->createBlock('Amasty\Meta\Block\Adminhtml\Config\Edit\Tab\General')
                        ->setTitle($name)->toHtml(),
            )
        );

        $name = __('Products in Main Category');
        $this->addTab('products', array(
                'label'   => $name,
                'content' => $this->getLayout()->createBlock('Amasty\Meta\Block\Adminhtml\Config\Edit\Tab\Product')
                        ->setTitle($name)->toHtml(),
            )
        );

        $name = __('Sub Categories');
        $this->addTab('categorySub', array(
                'label'   => $name,
                'content' => $this->getLayout()->createBlock('Amasty\Meta\Block\Adminhtml\Config\Edit\Tab\Category')
                        ->setTitle($name)->toHtml(),
            )
        );

        $name = __('Products in Sub Categories');
        $this->addTab('productSub', array(
                'label'   => $name,
                'content' => $this->getLayout()->createBlock('Amasty\Meta\Block\Adminhtml\Config\Edit\Tab\ProductSub')
                        ->setTitle($name)->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}