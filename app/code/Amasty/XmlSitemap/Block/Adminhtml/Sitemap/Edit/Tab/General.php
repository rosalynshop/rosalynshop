<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Block\Adminhtml\Sitemap\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class General extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    private $store;

    /**
     * @var \Amasty\XmlSitemap\Helper\Data
     */
    private $helper;

    /**
     * General constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $store
     * @param \Amasty\XmlSitemap\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $store,
        \Amasty\XmlSitemap\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->store = $store;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('General');
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

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('amxsmlsitemap_form_general', ['legend' => __('General')]);

        $model = $this->_coreRegistry->registry('amxmlsitemap_profile');

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField('title', 'text', [
            'label' => __('Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ]);

        $fieldset->addField('store_id', 'select', [
            'label' => __('Stores'),
            'name' => 'store_id',
            'values' => $this->store->getStoreValuesForForm()
        ]);

        $fieldset->addField(
            'folder_name',
            'text',
            [
                'label' => __('Path to sitemap file'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'folder_name',
                'note' => __('Example: "sitemap/sitemap1.xml".'
                    . ' Make sure path is writable and accessible through internet'),
            ]
        );

        $fieldset->addField(
            'max_items',
            'text',
            [
                'label' => __('Max Items Per File'),
                'name' => 'max_items',
                'note' => __('If exceed, index file will be created.'
                    . ' Read more at <a target="_blank" href="%1">link</a>', 'https://support.google.com/webmasters/answer/71453?hl=en')
            ]
        );

        $fieldset->addField('max_file_size', 'text', [
            'label' => __('Max File Size (kB)'),
            'name' => 'max_file_size',
            'note' => __('If exceed, index file will be created.'
                . ' Read more at <a target="_blank" href="%1">link</a>', 'https://support.google.com/webmasters/answer/71453?hl=en')
        ]);

        $fieldset->addField('exclude_urls', 'textarea', [
            'label' => __('Exclude URLs'),
            'name' => 'exclude_urls',
            'note' => __('URL to exclude, one per line')
        ]);

        $fieldset->addField('date_format', 'select', [
            'label' => __('Date format'),
            'name' => 'date_format',
            'title' => __('Date format'),
            'values' => $this->helper->getDateFormats()
        ]);

        $form->addValues($model->getData());

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
