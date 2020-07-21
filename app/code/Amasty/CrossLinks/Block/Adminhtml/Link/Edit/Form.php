<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Block\Adminhtml\Link\Edit;

/**
 * Class Form
 * @package Amasty\CrossLinks\Block\Adminhtml\Link\Edit
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $sourceYesNo;

    /**
     * @var \Amasty\CrossLinks\Model\Source\ReferenceType
     */
    protected $sourceReferenceType;

    /**
     * @var \Amasty\CrossLinks\Model\Source\TargetType
     */
    protected $sourceTargetType;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNo
     * @param \Amasty\CrossLinks\Model\Source\ReferenceType $referenceType
     * @param \Amasty\CrossLinks\Model\Source\TargetType $targetType
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Amasty\CrossLinks\Model\Source\ReferenceType $referenceType,
        \Amasty\CrossLinks\Model\Source\TargetType $targetType,
        array $data = []
    ) {
        $this->sourceYesNo = $yesNo;
        $this->sourceReferenceType = $referenceType;
        $this->systemStore = $systemStore;
        $this->sourceTargetType = $targetType;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('link_form');
        $this->setTitle(__('Link Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_link');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('link_id', 'hidden', ['name' => 'link_id']);
        }

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Active'),
                'title' => __('Active'),
                'name' => 'status',
                'options' => $this->sourceYesNo->toArray()
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            ['name' => 'title', 'label' => __('Link Title'), 'title' => __('Link Title'), 'required' => true]
        );

        $fieldset->addField(
            'link_target',
            'select',
            [
                'label' => __('Target'),
                'title' => __('Target'),
                'name' => 'link_target',
                'options' => $this->sourceTargetType->toArray()
            ]
        );

        $fieldset->addField(
            'keywords',
            'textarea',
            ['name' => 'keywords', 'label' => __('Keywords'), 'title' => __('Keywords'), 'required' => true]
        );

        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name'     => 'store_ids[]',
                'label'    => __('Store Views'),
                'title'    => __('Store Views'),
                'required' => true,
                'values'   => $this->systemStore->getStoreValuesForForm(false, true),
            ]
        );

        $fieldset->addField(
            'reference_type',
            'select',
            [
                'label' => __('Reference'),
                'title' => __('Reference'),
                'name' => 'reference_type',
                'options' => $this->sourceReferenceType->toArray()
            ]
        );

        $referenceResource = $fieldset->addField(
            'reference_resource',
            'text',
            [
                'name' => 'reference_resource',
                'label' => __('Reference Resource'),
                'title' => __('Reference Resource'),
                'required' => true
            ]
        );

        $referenceResource->setRenderer(
            $this->getLayout()
                ->createBlock(\Amasty\CrossLinks\Block\Adminhtml\Link\Edit\Form\Renderer\ReferenceResource::class)
        );

        $fieldset->addField(
            'replacement_limit',
            'text',
            [
                'name' => 'replacement_limit',
                'label' => __('Replacement Limit Per Page'),
                'title' => __('Replacement Limit Per Page'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'priority',
            'text',
            [
                'name' => 'priority',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'note' => __('Set zero value for highest priority'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'is_nofollow',
            'select',
            [
                'label' => __('Nofollow'),
                'title' => __('Nofollow'),
                'name' => 'is_nofollow',
                'options' => $this->sourceYesNo->toArray()
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
