<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Log\Reports;

use Magento\Backend\Block\Template;

/**
 * Class Charts
 *
 * @package Amasty\Fpc\Block\Adminhtml\Log\Reports
 */
class Charts extends Template
{
    protected $_template = 'Amasty_Fpc::log/charts.phtml';

    /**
     * @var \Amasty\Fpc\Model\ResourceModel\Log\CollectionFactory
     */
    private $logCollectionFactory;

    /**
     * @var \Amasty\Fpc\Helper\Http
     */
    private $httpHelper;

    public function __construct(
        Template\Context $context,
        \Amasty\Fpc\Model\ResourceModel\Log\CollectionFactory $logCollectionFactory,
        \Amasty\Fpc\Helper\Http $httpHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logCollectionFactory = $logCollectionFactory;
        $this->httpHelper = $httpHelper;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getChartData($type)
    {
        /** @var \Amasty\Fpc\Model\ResourceModel\Log\Collection $collection */
        $collection = $this->logCollectionFactory->create();
        $totalRecords = $collection->count();

        $collection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(
                [
                    $type,
                    'num' => new \Zend_Db_Expr("COUNT($type)")
                ]
            )->group($type);

        $data = $collection->getConnection()->fetchAll($collection->getSelect());
        $this->getPercentsAndDescription($data, $totalRecords);

        return $data;
    }

    /**
     * @param array $statuses
     * @param int $total
     */
    private function getPercentsAndDescription(&$statuses, $total)
    {
        $codes = $this->httpHelper->getStatusCodes();

        foreach ($statuses as &$status) {
            $percent = $status['num'] / $total * 100;
            $status['percent'] = $percent;

            if (isset($status['status']) && array_key_exists($status['status'], $codes)) {
                $status['description'] = $codes[$status['status']];
            }
        }
    }
}
