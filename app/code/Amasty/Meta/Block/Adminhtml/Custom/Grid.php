<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Block\Adminhtml\Custom;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Amasty\Meta\Model\Config
     */
    protected $config;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Amasty\Meta\Model\Config $config,
        $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('configGrid');
        $this->setDefaultSort('config_id');
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->config->getCustomCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('config_id', array(
          'header'    => __('ID'),
          'align'     => 'right',
          'width'     => '50px',
          'index'     => 'config_id',
        ));

        $this->addColumn('priority', array(
            'header'    => __('Priority'),
             'index'     => 'priority',
                        'align'     => 'right',
                        'width'     => '50px',
        ));

        $this->addColumn('custom_url', array(
            'header'    => __('URL'),
            'index'     => 'custom_url'
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'     => __('Store'),
                'index'      => 'store_id',
                'type'       => 'store',
                'renderer'   => 'Amasty\Meta\Block\Adminhtml\Widget\Grid\Column\Renderer\Store',
                'filter'     => 'Amasty\Meta\Block\Adminhtml\Widget\Grid\Column\Filter\Store',
                'store_view' => true,
                'sortable'   => false
            ));
        }

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
