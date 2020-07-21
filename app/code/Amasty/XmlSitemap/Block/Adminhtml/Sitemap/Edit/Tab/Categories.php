<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Block\Adminhtml\Sitemap\Edit\Tab;

class Categories extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /** @var
     * \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     */
    private $yesnoFactory;

    /**
     * @var \Amasty\XmlSitemap\Helper\Data $helper
     */
    private $helper;

    /**
     * Categories constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     * @param \Amasty\XmlSitemap\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Amasty\XmlSitemap\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->yesnoFactory = $yesnoFactory;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Categories');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Categories');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('amxmlsitemap_profile');

        $yesNo = $this->yesnoFactory->create()->toOptionArray();

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('amxmlsitemap_form_categories', ['legend' => __('Categories')]);

        $fieldset->addField('categories', 'select', [
            'label' => __('Include categories'),
            'name' => 'categories',
            'title' => __('Include categories'),
            'values' => $yesNo
        ]);

        $fieldset->addField('hreflang_category', 'select', [
            'label' => __('Add Hreflang Tags'),
            'title' => __('Add Hreflang Tags'),
            'name' => 'hreflang_category',
            'values' => $yesNo
        ]);

        $fieldset->addField('categories_thumbs', 'select', [
            'label' => __('Add Images'),
            'name' => 'categories_thumbs',
            'title' => __('Add Images'),
            'values' => $yesNo
        ]);

        $fieldset->addField('categories_priority', 'text', [
            'label' => __('Priority'),
            'name' => 'categories_priority',
            'note' => __('0.01-0.99'),
            'class' => 'validate-number validate-number-range number-range-0.01-0.99'
        ]);

        $fieldset->addField('categories_frequency', 'select', [
            'label' => __('Frequency'),
            'name' => 'categories_frequency',
            'title' => __('Frequency'),
            'values' => $this->helper->getFrequency()
        ]);

        $fieldset->addField('categories_modified', 'select', [
            'label' => __('Include Last Modified'),
            'name' => 'categories_modified',
            'title' => __('Include Last Modified'),
            'values' => $yesNo
        ]);

        $form->addValues($model->getData());

        $this->setChild('form_after', $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap('categories', 'categories')
            ->addFieldMap('hreflang_category', 'hreflang_category')
            ->addFieldMap('categories_thumbs', 'categories_thumbs')
            ->addFieldMap('categories_priority', 'categories_priority')
            ->addFieldMap('categories_frequency', 'categories_frequency')
            ->addFieldMap('categories_modified', 'categories_modified')
            ->addFieldDependence('hreflang_category', 'categories', 1)
            ->addFieldDependence('categories_thumbs', 'categories', 1)
            ->addFieldDependence('categories_priority', 'categories', 1)
            ->addFieldDependence('categories_frequency', 'categories', 1)
            ->addFieldDependence('categories_modified', 'categories', 1));

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
