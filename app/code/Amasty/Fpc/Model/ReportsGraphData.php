<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Amasty\Fpc\Helper\Http as HttpHelper;
use Amasty\Fpc\Model\ResourceModel\Reports\Collection;
use Amasty\Fpc\Model\ResourceModel\Log as LogResource;

class ReportsGraphData
{
    /**
     * @var ResourceModel\Reports\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var LogResource
     */
    private $logResource;
    /**
     * @var HttpHelper
     */
    private $httpHelper;

    public function __construct(
        \Amasty\Fpc\Model\ResourceModel\Reports\CollectionFactory $collectionFactory,
        LogResource $logResource,
        HttpHelper $httpHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->logResource = $logResource;
        $this->httpHelper = $httpHelper;
    }

    public function getDataForWarmed($key = Collection::DATE_TYPE_DAY)
    {
        $collection = $this->collectionFactory->create();
        $collection->prepareCollectionForWarmedPages($key);
        $stats = $collection->getConnection()->fetchAll($collection->getSelect());

        return $stats;
    }

    public function getDataForStatus($key = Collection::DATE_TYPE_DAY)
    {
        $result = [];
        $stats = $this->logResource->getStatsByStatus($key);

        $statusColors = [
            HttpHelper::STATUS_ALREADY_CACHED => 'gray',
            200 => 'green',
            500 => 'red',
            404 => 'orange',
        ];

        foreach ($stats as $code => $count) {
            $status = $this->httpHelper->getStatusCodeDescription($code);

            if ($code != HttpHelper::STATUS_ALREADY_CACHED) {
                $status = $code . ' ' . $status;
            }

            $row = [
                'status' => $status,
                'count'  => $count,
                'code'   => $code
            ];

            if ($code == 200) {
                $row['suffix'] = ' Warmed Pages';
            }

            if (isset($statusColors[$code])) {
                $row['color'] = $statusColors[$code];
            } // else assign random color

            $result [] = $row;
        }

        return $result;
    }
}
