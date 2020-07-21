<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs;

use Amasty\AdminActionsLog\Helper\Data;

class Product extends \Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs\DefaultItemColumns
{

    /**
     * @var \Amasty\AdminActionsLog\Model\ResourceModel\Log\CollectionFactory
     */
    protected $logCollection;

    /**
     * @var \Amasty\AdminActionsLog\Model\ResourceModel\LogDetails\CollectionFactory
     */
    protected $logDetailsCollection;

    /**
     * Product constructor.
     * @param \Amasty\AdminActionsLog\Model\ResourceModel\Log\CollectionFactory $logCollection
     * @param \Amasty\AdminActionsLog\Model\ResourceModel\LogDetails\CollectionFactory $logDetailsCollection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        \Amasty\AdminActionsLog\Model\ResourceModel\Log\CollectionFactory $logCollection,
        \Amasty\AdminActionsLog\Model\ResourceModel\LogDetails\CollectionFactory $logDetailsCollection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $objectManager, $backendHelper, $registry, $helper, $data);
        $this->logCollection = $logCollection;
        $this->logDetailsCollection = $logDetailsCollection;
    }

    protected function _prepareCollection()
    {
        $elementId = $this->getRequest()->getParam('id');
        $collection = $this->logCollection->create();
        $collection->getSelect()
            ->joinLeft(
                [
                    'r' => $this->logDetailsCollection->create()
                        ->getMainTable()
                ],
                'main_table.id = r.log_id',
                [
                    'is_logged' => 'MAX(r.log_id)'
                ]
            )
            ->where("element_id = ?", $elementId)
            ->where("category = ?", 'catalog/product')
            ->group('r.log_id');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
}
