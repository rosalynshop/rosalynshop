<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Block\Adminhtml\Custom\Edit\Tab;

use Magento\Framework\Data\FormFactory;

class General extends \Magento\Backend\Block\Widget\Form
{

    /**
     * @var \Amasty\Meta\Model\System\Store
     */
    protected $store;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * General constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param FormFactory $formFactory
     * @param \Amasty\Meta\Model\System\Store $store
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        FormFactory $formFactory,
        \Amasty\Meta\Model\System\Store $store,
        array $data = []
    ) {
        $this->store = $store;
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $this->setForm($form);

        $fldCond = $form->addFieldset(
            'attr',
            ['legend' => __('General')]
        );

        $fldCond->addField(
            'custom_url',
            'text',
            [
                'label' => __('Page Url'),
                'name'  => 'custom_url',
                'note'  => __('You can use `*` symbol for specify url pattern.'
                    . ' Please use url without domain name. Ex: gear/watches.html')
            ]
        );

        $fldCond->addField(
            'priority',
            'text',
            [
                'label'  => __('Priority'),
                'name'   => 'priority',
                'values' => $this->store->getStoreValuesForForm(true),
                'class'  => 'validate-digits',
                'value'  => 0
            ]
        );

        if (! $this->_storeManager->isSingleStoreMode()) {
            $fldCond->addField(
                'store_id',
                'select',
                [
                    'label'  => __('Apply For'),
                    'name'   => 'store_id',
                    'values' => $this->store->getStoreValuesForForm(true)
                ]
            );
        }

        //set form values
        $model = $this->_coreRegistry->registry('ammeta_config');
        $form->setValues($model->getData());
        return parent::_prepareForm();
    }
}