<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Block\Adminhtml\Sitemap\Edit\Tab;

class Blog extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * @var \Magento\Framework\Module\Manager $moduleManager
     */
    private $moduleManager;

    /**
     * Landing constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     * @param \Amasty\XmlSitemap\Helper\Data $helper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Amasty\XmlSitemap\Helper\Data $helper,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->yesnoFactory = $yesnoFactory;
        $this->helper = $helper;
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Blog Pages');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Blog Pages');
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
        return !$this->moduleManager->isEnabled('Amasty_Blog');
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('amxmlsitemap_profile');

        $yesno = $this->yesnoFactory->create()->toOptionArray();

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('amxmlsitemap_form_blog', ['legend' => __('Blog Pages')]);

        $fieldset->addField('blog', 'select', [
            'label' => __('Include Blog Pages'),
            'name' => 'blog',
            'title' => __('Include Blog Pages'),
            'values' => $yesno
        ]);

        $fieldset->addField('blog_priority', 'text', [
            'label' => __('Priority'),
            'name' => 'blog_priority',
            'note' => __('0.01-0.99'),
            'class' => 'validate-number validate-number-range number-range-0.01-0.99'
        ]);

        $fieldset->addField('blog_frequency', 'select', [
            'label' => __('Frequency'),
            'name' => 'blog_frequency',
            'title' => __('Frequency'),
            'values' => $this->helper->getFrequency()
        ]);

        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
