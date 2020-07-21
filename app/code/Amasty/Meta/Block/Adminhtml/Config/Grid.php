<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Block\Adminhtml\Config;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Amasty\Meta\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    private $category;

    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Category $category,
        \Amasty\Meta\Helper\Data $helper,
        \Amasty\Meta\Model\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->config = $config;
        $this->_storeManager = $context->getStoreManager();
        $this->category = $category;
        $this->helper = $helper;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('configGrid');
        $this->setDefaultSort('config_id');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->config->getCollection();
        $root = $collection->getConnection()->quote(' - ' . __('Root'));
        $nameAttribute = $this->category->getAttribute('name');
        $nameAttributeLinkField = $nameAttribute->getEntity()->getLinkField();

        $collection->getSelect()
            ->joinLeft(
                ['cce' => $collection->getTable('catalog_category_entity')],
                'cce.entity_id = main_table.category_id',
                []
            )->joinLeft(
                ['att' => $nameAttribute->getBackend()->getTable()],
                $collection->getConnection()->quoteInto(
                    'att.' . $nameAttributeLinkField . ' = cce.entity_id AND att.attribute_id = ?',
                    $nameAttribute->getId()
                ),
                ['category_name' => new \Zend_Db_Expr("COALESCE(value, $root)")]
            )->group('main_table.config_id');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('config_id',
            array(
                'header' => __('ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'config_id',
            ));

        $this->addColumn('category_id',
            array(
                'header' => __('Category'),
                'index' => 'category_id',
                'renderer' => 'Amasty\Meta\Block\Adminhtml\Widget\Grid\Column\Renderer\Category',
                'filter' => 'Magento\Backend\Block\Widget\Grid\Column\Filter\Select',
                'options' => $this->helper->getTree(true),
            ));


        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => __('Store'),
                'index' => 'store_id',
                'filter_index' => 'main_table.store_id',
                'type' => 'store',
                'renderer' => 'Amasty\Meta\Block\Adminhtml\Widget\Grid\Column\Renderer\Store',
                'filter' => 'Amasty\Meta\Block\Adminhtml\Widget\Grid\Column\Filter\Store',
                'store_view' => true,
                'sortable' => false
            ));
        }

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
