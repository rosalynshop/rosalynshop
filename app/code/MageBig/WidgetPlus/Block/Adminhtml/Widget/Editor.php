<?php

namespace MageBig\WidgetPlus\Block\Adminhtml\Widget;

class Editor extends \Magento\Backend\Block\Widget\Form\Element
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var Factory
     */
    protected $_factoryElement;

    /**
     * @param Factory           $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param array             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        $data = []
    ) {
        $this->_factoryElement = $factoryElement;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $editor = $this->_factoryElement->create('editor', ['data' => $element->getData()])
            ->setLabel('')
            ->setForm($element->getForm())
            ->setWysiwyg(true)
            ->setConfig($this->_wysiwygConfig->getConfig([
                'add_variables' => false,
                'add_widgets' => true,
                'height' => '200px',
            ]));

        if ($element->getRequired()) {
            $editor->addClass('required-entry');
        }

        $element->setData(
            'after_element_html',
            $editor->getElementHtml().$this->_getAfterElementHtml()
        );

        return $element;
    }

    /**
     * @return string
     */
    protected function _getAfterElementHtml()
    {
        $config = $this->_wysiwygConfig->getConfig();
        $config = json_encode($config->getData());
        $html = <<<HTML
<script>
require([
    'jquery',
    'mage/adminhtml/wysiwyg/tiny_mce/setup'
], function(jQuery){

var config = $config,
    editor;

jQuery.extend(config, {
    settings: {
        theme_advanced_buttons1 : 'bold,italic,|,justifyleft,justifycenter,justifyright,|,' +
            'fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code',
        theme_advanced_buttons2: null,
        theme_advanced_buttons3: null,
        theme_advanced_buttons4: null,
        theme_advanced_statusbar_location: null
    },
    files_browser_window_url: false
});

editor = new tinyMceWysiwygSetup(
    '{$this->getHtmlId()}',
    config
);
editor.turnOn();

jQuery('#{$this->getHtmlId()}')
    .addClass('wysiwyg-editor')
    .data(
        'wysiwygEditor',
        editor
    );
});
</script>
HTML;

        return $html;
    }
}
