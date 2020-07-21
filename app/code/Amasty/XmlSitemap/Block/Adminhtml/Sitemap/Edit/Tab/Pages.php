<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Block\Adminhtml\Sitemap\Edit\Tab;

class Pages extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     */
    private $yesnoFactory;

    /**
     * @var \Amasty\XmlSitemap\Helper\Data $helper
     */
    private $helper;

    /**
     * Products constructor.
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
        return __('Pages');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Pages');
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

        $fieldset = $form->addFieldset('amxmlsitemap_form_pages', ['legend' => __('Pages')]);

        $fieldset->addField('pages', 'select', [
            'label' => __('Include pages'),
            'name' => 'pages',
            'title' => __('Include pages'),
            'values' => $yesNo
        ]);

        $fieldset->addField('hreflang_cms', 'select', [
            'label' => __('Add Hreflang Tags'),
            'title' => __('Add Hreflang Tags'),
            'name' => 'hreflang_cms',
            'values' => $yesNo
        ]);

        $fieldset->addField('pages_priority', 'text', [
            'label' => __('Priority'),
            'name' => 'pages_priority',
            'note' => __('0.01-0.99'),
            'class' => 'validate-number validate-number-range number-range-0.01-0.99'
        ]);

        $fieldset->addField('pages_frequency', 'select', [
            'label' => __('Frequency'),
            'name' => 'pages_frequency',
            'title' => __('Frequency'),
            'values' => $this->helper->getFrequency()
        ]);

        $fieldset->addField('pages_modified', 'select', [
            'label' => __('Include Last Modified'),
            'name' => 'pages_modified',
            'title' => __('Include Last Modified'),
            'values' => $yesNo
        ]);

        $fieldset->addField('exclude_cms_aliases', 'textarea', [
            'label' => __('Exclude CMS pages'),
            'name' => 'exclude_cms_aliases',
            'note' => __('URL Keys of CMS Pages to exclude, one per line')
        ]);

        $form->addValues($model->getData());

        $this->setChild('form_after', $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap('pages', 'pages')
            ->addFieldMap('hreflang_cms', 'hreflang_cms')
            ->addFieldMap('pages_priority', 'pages_priority')
            ->addFieldMap('pages_frequency', 'pages_frequency')
            ->addFieldMap('pages_modified', 'pages_modified')
            ->addFieldMap('exclude_cms_aliases', 'exclude_cms_aliases')
            ->addFieldDependence('hreflang_cms', 'pages', 1)
            ->addFieldDependence('pages_priority', 'pages', 1)
            ->addFieldDependence('pages_frequency', 'pages', 1)
            ->addFieldDependence('pages_modified', 'pages', 1)
            ->addFieldDependence('exclude_cms_aliases', 'pages', 1));

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
