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
use Amasty\Fpc\Model\ResourceModel\Reports\Collection;

class Status extends Report implements TabInterface
{
    protected $_template = 'report/status.phtml';

    /**
     * @var ReportsGraphData
     */
    private $graphData;

    public function __construct(
        Template\Context $context,
        ReportsGraphData $graphData,
        array $data = []
    ) {
        parent::__construct($context, $data);
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
        return __('Status Codes');
    }

    protected function _getGraphData()
    {
        return $this->graphData->getDataForStatus(Collection::DATE_TYPE_DAY);
    }
}
