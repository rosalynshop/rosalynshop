<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Block\Adminhtml\Link;

/**
 * Class Edit
 * @package Amasty\CrossLinks\Block\Adminhtml\Group
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'link_id';
        $this->_blockGroup = 'Amasty_CrossLinks';
        $this->_controller = 'adminhtml_link';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Link'));
        $this->buttonList->update('delete', 'label', __('Delete Link'));

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            -100
        );
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('current_link') !== null) {
            $linkTitle = $this->coreRegistry->registry('current_link')->getTitle();
            return __("Edit Link '%1'", $this->escapeHtml($linkTitle));
        } else {
            return __('New Link');
        }
    }
}
