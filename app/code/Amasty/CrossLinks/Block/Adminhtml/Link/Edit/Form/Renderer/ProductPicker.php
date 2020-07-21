<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Block\Adminhtml\Link\Edit\Form\Renderer;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use \Magento\Catalog\Model\Product\Visibility as ProductVisibility;

/**
 * Class ProductPicker
 * @package Amasty\CrossLinks\Block\Adminhtml\Link\Edit\Form\Renderer
 */
class ProductPicker extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Store hidden category ids field id
     *
     * @var string
     */
    protected $_elementValueId = '';

    /**
     * ProductPicker constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Define grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'sku',
            ['header' => __('Sku'), 'type' => 'text', 'index' => 'sku', 'escape' => true]
        );

        $this->addColumn(
            'name',
            ['header' => __('Product'), 'type' => 'text', 'index' => 'name', 'escape' => true]
        );

        return parent::_prepareColumns();
    }

    /**
     * Set defaults
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productGrid');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_elementValueId = "{$element->getId()}";

        $element->setValue('')->setValueClass('value2');
        $element->setData('css_class', 'grid-chooser');
        $element->setData('no_wrap_as_addon', true);

        return $element;
    }

    /**
     * Disable mass action functionality
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Adds additional parameter to URL for loading only categorys grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'amasty_crosslinks/product/picker',
            [
                'product_grid' => true,
                '_current' => true,
                'uniq_id' => $this->getId()
            ]
        );
    }

    /**
     * Set categories' positions of saved categories
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToFilter('status', ['eq' => ProductStatus::STATUS_ENABLED]);
        $collection->addAttributeToFilter(
            'visibility',
            ['in' => [ProductVisibility::VISIBILITY_IN_CATALOG, ProductVisibility::VISIBILITY_BOTH]]
        );
        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getMultipleRows($item)
    {
        return null;
    }
}
