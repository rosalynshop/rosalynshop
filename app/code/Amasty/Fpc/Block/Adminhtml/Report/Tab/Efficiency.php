<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Report\Tab;

use Amasty\Fpc\Helper\Http as HttpHelper;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Template;

class Efficiency extends Report implements TabInterface
{
    protected $_template = 'report/efficiency.phtml';

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('Efficiency Report');
    }

    /**
     * Because of abstract method _getGraphData of Report
     * generating of data happens in DataProvider of ui component
     * need to return true
     *
     * @return true
     */
    protected function _getGraphData()
    {
        return true;
    }
}
