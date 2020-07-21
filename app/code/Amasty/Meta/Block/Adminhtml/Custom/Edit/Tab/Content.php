<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Block\Adminhtml\Custom\Edit\Tab;
use Magento\Framework\Data\FormFactory;

class Content extends \Magento\Backend\Block\Widget\Form
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
     * @var \Amasty\Meta\Helper\Data
     */
    protected $dataHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Meta\Helper\Data $dataHelper,
        FormFactory $formFactory,
        \Amasty\Meta\Model\System\Store $store,
        array $data = []
    ) {
        $this->store = $store;
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $this->setForm($form);

        $fldCond = $form->addFieldset(
            'attr',
            array('legend' => __('Content'))
        );

        $fldCond->addField('custom_meta_title',
            'text',
            array(
                'label' => __('Title'),
                'name'  => 'custom_meta_title'
            )
        );

        $fldCond->addField('custom_meta_description',
            'textarea',
            array(
                'label' => __('Meta Description'),
                'name'  => 'custom_meta_description'
            )
        );

        $fldCond->addField('custom_meta_keywords',
            'textarea',
            array(
                'label' => __('Keywords'),
                'name'  => 'custom_meta_keywords'
            )
        );

        $fldCond->addField('custom_canonical_url',
            'text',
            array(
                'label' => __('Canonical Url'),
                'name'  => 'custom_canonical_url',
                'note'  => __('We can only replace Canonical Url. Extension will not replace it if canonical url is missing. To enable showing canonical please go to Stores -> Configuration -> Catalog -> Search Engine Optimization')
            )
        );

        $fldCond->addField('custom_robots',
            'select',
            array(
                'label'  => __('Robots'),
                'name'   => 'custom_robots',
                'values' => $this->dataHelper->getRobotOptions()
            )
        );

        $fldCond->addField('custom_h1_tag',
            'text',
            array(
                'label' => __('H1 Tag'),
                'name'  => 'custom_h1_tag'
            )
        );

        //set form values
        $model = $this->_coreRegistry->registry('ammeta_config');
        $form->setValues($model->getData());

        return parent::_prepareForm();
    }
}
