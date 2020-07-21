<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Report\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Template;
use Amasty\Fpc\Model\ReportsGraphData;

class Crawled extends Report implements TabInterface
{
    protected $_template = 'report/crawled.phtml';

    /**
     * @var \Amasty\Fpc\Model\ResourceModel\Log
     */
    private $logResource;
    /**
     * @var ReportsGraphData
     */
    private $graphData;

    public function __construct(
        Template\Context $context,
        \Amasty\Fpc\Model\ResourceModel\Log $logResource,
        ReportsGraphData $graphData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logResource = $logResource;
        $this->graphData = $graphData;
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('Warmed Pages');
    }

    protected function _getGraphData()
    {
        return $this->graphData->getDataForWarmed();
    }
}
