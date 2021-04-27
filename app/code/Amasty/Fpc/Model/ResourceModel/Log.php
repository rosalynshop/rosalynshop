<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Amasty\Fpc\Model\ResourceModel\Reports\Collection;

class Log extends AbstractDb
{
    /**
     * @var Reports\Collection
     */
    private $reportsCollection;

    public function __construct(
        \Amasty\Fpc\Model\ResourceModel\Reports\Collection $reportsCollection,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->reportsCollection = $reportsCollection;
    }

    protected function _construct()
    {
        $this->_init('amasty_fpc_log', 'id');
    }

    public function deleteWithLimit($limit)
    {
        if ($limit <= 0) {
            return;
        }

        $limit = (int)$limit;

        $query = "DELETE FROM `{$this->getMainTable()}` LIMIT $limit";

        $this->getConnection()->query($query);
    }

    public function flush()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    public function getStatsByStatus($code = Collection::DATE_TYPE_DAY)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['status', 'COUNT(id)'])
            ->group('status');
        $this->reportsCollection->addWhereCondition($code, $select, 'created_at');

        return $this->getConnection()->fetchPairs($select);
    }

    public function getStatsByDay()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['period' => 'DATE(created_at)', 'count' => 'COUNT(id)'])
            ->order('period')
            ->group('DATE(created_at)');

        return $this->getConnection()->fetchAll($select);
    }
}
