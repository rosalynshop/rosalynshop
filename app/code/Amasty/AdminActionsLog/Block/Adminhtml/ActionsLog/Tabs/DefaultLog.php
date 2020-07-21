<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs;

use Amasty\AdminActionsLog\Helper\Data;

abstract class DefaultLog extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_objectManager;
    protected $_coreRegistry;

    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('actionsLogGrid');
    }

    public function decorateStatus($value, $row, $column)
    {
        return '<span class="amaudit-' . $value . '">' . $value . '</span>';
    }

    public function getOpenElementUrl($value, $row, $column)
    {
        return $this->helper->showOpenElementUrl($row);
    }

    public function showActions($value, $row, $column)
    {
        $preview = "";

        if (($row->getType() == "Edit" || $row->getType() == "New" || $row->getType() == 'Restore')) {
            $preview = '<a class="amaudit-preview"
            onclick="previewChanges.open(\'' . $this->_backendHelper
                    ->getUrl('amaudit/actionslog/preview') . '\', \'' . $row->getId() . '\');">'
                . __('Preview Changes') . '</a><span id="' . $row->getId() . '_editor""></span><br>';
        }

        return $preview . '<a href="'
            . $this->getUrl('amaudit/actionslog/edit', ['id' => $row->getId()])
            . '"><span>' . __('View Details') . '</span></a>';
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['id' => $row->getId()]
        );
    }
}
