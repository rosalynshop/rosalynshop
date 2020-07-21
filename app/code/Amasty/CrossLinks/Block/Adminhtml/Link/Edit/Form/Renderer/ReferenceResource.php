<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Block\Adminhtml\Link\Edit\Form\Renderer;

use Magento\Framework\Data\Form\Element\Factory;
use \Amasty\CrossLinks\Model\Source\ReferenceType;

/**
 * Class ReferenceResource
 * @package Amasty\CrossLinks\Block\Adminhtml\Link\Edit\Form\Renderer
 */
class ReferenceResource extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var string
     */
    protected $_template = 'form/renderer/reference_resource.phtml';

    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * ReferenceResource constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Factory $elementFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->elementFactory = $elementFactory;
        $this->registry = $registry;
    }

    /**
     * @return string
     */
    public function getCategoryPickerHtml()
    {
        return $this->getLayout()->createBlock(
            \Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser::class,
            '',
            [
                'data' => [
                    'id' => $this->_element->getHtmlId() . '_category_picker',
                    'node_click_listener' => $this->getCategoryClickListenerJs(),
                    'use_massaction' => false,
                ]
            ]
        )->toHtml();
    }

    /**
     * @return string
     */
    public function getProductPickerHtml()
    {
        return $this->getLayout()->createBlock(
            \Amasty\CrossLinks\Block\Adminhtml\Link\Edit\Form\Renderer\ProductPicker::class,
            '',
            [
                'data' => [
                    'id' => $this->_element->getHtmlId() . '_product_picker',
                    'row_click_callback' => $this->getProductPickerRowClickCallbackJs(),
                ]
            ]
        )->toHtml();
    }

    /**
     * Get hidden filed html, which contains resource_reference for product and category
     *
     * @return string
     */
    public function getCatalogFieldHtml()
    {
        $hidden = $this->elementFactory->create('hidden', []);
        $hidden->setClass('catalog-field')->setName('reference_resource')->setRequired(true);
        $hidden->setId($this->_element->getHtmlId() . '_catalog_field')->setForm($this->_element->getForm());
        return $hidden->getElementHtml();
    }

    /**
     * Category Tree node onClick listener js function
     *
     * @return string
     */
    public function getCategoryClickListenerJs()
    {
        return '
            function (node, e) {                 
                var nodeId = node.attributes.id != "none" ? node.attributes.id : false;
                var rootIds = ' . \Zend_Json::encode($this->getRootIds()) . ';
                if(rootIds.indexOf(nodeId) != -1) {
                    return;
                }                               
                ResourceManager.setResourceValue(nodeId);
                ResourceManager.showResourceName(nodeId ? node.text : false);
            }
        ';
    }

    /**
     * Product Grid row onClick listener js function
     *
     * @return string
     */
    public function getProductPickerRowClickCallbackJs()
    {
        return '
            function (grid, event) {
                var trElement   = Event.findElement(event, "tr");                
                for (var i = 0; i < trElement.childNodes.length; i++) {
                    if (typeof trElement.childNodes[i].classList != "undefined") {
                        if (trElement.childNodes[i].classList.contains("col-entity_id")) {
                            ResourceManager.setResourceValue(trElement.childNodes[i].innerText);
                        }
                        if (trElement.childNodes[i].classList.contains("col-name")) {
                            ResourceManager.showResourceName(trElement.childNodes[i].innerText);
                        }
                    }                    
                }   
            }
        ';
    }

    /**
     * Getting resource of reference(Product, Category)
     *
     * @return mixed
     */
    protected function getReferenceResource()
    {
        $link = $this->registry->registry('current_link');
        switch ($link->getReferenceType()){
            case ReferenceType::REFERENCE_TYPE_PRODUCT: $resource = $link->getProduct(); break;
            case ReferenceType::REFERENCE_TYPE_CATEGORY: $resource = $link->getCategory(); break;
            default: $resource = null;
        }
        return $resource;
    }

    /**
     * @return null|string
     */
    public function getReferenceResourceText()
    {
        if ($resource = $this->getReferenceResource()) {
            return $this->getReferenceResource()->getName();
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getReferenceResourceValue()
    {
        if ($resource = $this->getReferenceResource()) {
            return $this->getReferenceResource()->getId();
        }
        return null;
    }

    /**
     * Return ids of root categories as array
     *
     * @return array
     */
    public function getRootIds()
    {
        $ids = $this->getData('root_ids');
        if ($ids === null) {
            $ids = [\Magento\Catalog\Model\Category::TREE_ROOT_ID];
            foreach ($this->_storeManager->getGroups() as $store) {
                $ids[] = $store->getRootCategoryId();
            }
            $this->setData('root_ids', $ids);
        }
        return $ids;
    }
}
