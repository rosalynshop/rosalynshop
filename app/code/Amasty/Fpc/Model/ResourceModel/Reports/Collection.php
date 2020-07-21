<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\ResourceModel\Reports;

use Amasty\Fpc\Model\Reports;
use Amasty\Fpc\Model\ResourceModel\Reports as ReportsResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\Fpc\Setup\Operation\CreateReportsTable;
use Magento\Framework\DB\Select;

class Collection extends AbstractCollection
{
    const DATE_TYPE_ALL = 0;

    const DATE_TYPE_DAY = 1;

    const DATE_TYPE_WEEK = 2;

    const DATE_TYPE_MONTH = 3;

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _construct()
    {
        $this->_init(Reports::class, ReportsResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function prepareCollection($type = self::DATE_TYPE_DAY)
    {
        if ($type > self::DATE_TYPE_MONTH || $type < self::DATE_TYPE_ALL) {
            return false;
        }

        $select = $this->getSelect();
        $field = 'date';
        $select->reset()->from(
            $this->getResource()->getTable(CreateReportsTable::TABLE_NAME),
            [
                'dt' => 'date',
                'visited_at' => $this->prepareDateColumn($type, $field),
                'response_time' => "ROUND(AVG(`response`), 0)",
                'hit_response_time' => "ROUND(AVG(case when `status` like '%hit%' then `response` else null end), 0)",
                'miss_response_time' => "ROUND(AVG(case when `status` like '%miss%' then `response` else null end), 0)",
                'hits' => "(SUM(CASE WHEN `status` like '%hit%' then 1 else null end) / COUNT(status) * 100)",
                'misses' => "(SUM(case when `status` like '%miss%' then 1 else null end) / COUNT(`status`) * 100)",
                'visits' => "COUNT(`status`)"
            ]
        )->group($this->prepareDateColumn($type, $field));
        $this->addWhereCondition($type, $select, 'date');

        return $this;
    }

    /**
     * @param int $type
     *
     * @return $this
     * @throws \Zend_Db_Select_Exception
     */
    public function prepareCollectionForWarmedPages($type = self::DATE_TYPE_ALL)
    {
        $select = $this->getSelect();
        $subselect = clone $select;
        $subselect->reset()->from(
            ['log' => $this->getResource()->getTable('amasty_fpc_log')],
            [
                'warmed_at' => $this->prepareDateColumn($type, 'created_at'),
                'warmed' => 'SUM(case when `status` = 200 then 1 else null end)'
            ]
        )->where(
            'status = 200'
        )->group($this->prepareDateColumn($type, 'created_at'));
        $this->addWhereCondition($type, $subselect, 'created_at');

        $selectLeft = $this->getSelectForWarmedPages($type, 'left', clone $select, $subselect);
        $selectRight = $this->getSelectForWarmedPages($type, 'right', clone $select, $subselect);
        $select->reset()->union([$selectLeft, $selectRight])->order('dt ASC');

        return $this;
    }

    /**
     * @param int $ttl
     *
     * @return int
     */
    public function getHitRate($ttl)
    {
        $this->getSelect()->columns(
            [
                'rate' => new \Zend_Db_Expr(
                'SUM(case when `status` like "%hit%" then 1 else null end) / COUNT(status)'
                )
            ]
        );
        $this->addFieldToFilter('date', [
            'gt' => date('Y-m-d H:i:s', time() - $ttl),
        ]);

        $rate = round($this->getFirstItem()->getData('rate') * 100);

        return $rate;
    }

    /**
     * @param int $type
     * @param string $joinType
     * @param Select $select
     * @param Select $subselect
     *
     * @return mixed
     */
    private function getSelectForWarmedPages($type, $joinType, $select, $subselect)
    {
        $select->reset()->from(
            ['reports' => $this->getResource()->getTable(CreateReportsTable::TABLE_NAME)],
            [
                'dt' => 'date',
                'visited_at' => 'IF(' . $this->prepareDateColumn($type, 'date')
                    . 'IS NOT NULL, ' . $this->prepareDateColumn($type, 'date')
                    . ', sub.warmed_at)',
                'hits' => 'SUM(case when `status` like \'%hit%\' then 1 else null end)',
                'misses' => 'SUM(case when `status` like \'%miss%\' then 1 else null end)',
                'sub.warmed'
            ]
        );
        $joinType == 'left'
            ? $select->joinLeft(
            ['sub' => $subselect],
            $this->prepareDateColumn($type, 'date') . ' = sub.warmed_at',
            []
        )
            : $select->joinRight(
            ['sub' => $subselect],
            $this->prepareDateColumn($type, 'date') . ' = sub.warmed_at',
            []
        );
        $select->group($this->prepareDateColumn($type, 'date'));
        $this->addWhereCondition($type, $select, 'date');

        return $select;
    }

    /**
     * @param int $type
     *
     * @return string
     */
    private function prepareDateColumn($type, $field)
    {
        $expression = 'date';

        switch ($type) {
            case self::DATE_TYPE_DAY:
                $expression = "DATE_FORMAT(`$field`, '%b %D / %k:00')";
                break;
            case self::DATE_TYPE_WEEK:
            case self::DATE_TYPE_MONTH:
            case self::DATE_TYPE_ALL:
                $expression = "DATE_FORMAT(`$field`, '%b %D %Y')";
                break;
        }

        return $expression;
    }

    /**
     * @param int $type
     * @param Select $select
     */
    public function addWhereCondition($type, $select, $field)
    {
        switch ($type) {
            case self::DATE_TYPE_DAY:
                $select->where("CAST(`$field` as date) = CAST(CURRENT_TIMESTAMP as date)");
                break;
            case self::DATE_TYPE_WEEK:
                $select->where(
                    "(DATE(`$field`) <= CAST(CURRENT_TIMESTAMP as datetime))" .
                    " AND (DATE(`$field`) >= DATE_SUB(CAST(CURRENT_TIMESTAMP as datetime), INTERVAL 7 DAY))"
                );
                break;
            case self::DATE_TYPE_MONTH:
                $select->where(
                    "(DATE(`$field`) <= CAST(CURRENT_TIMESTAMP as datetime))" .
                    " AND (DATE(`$field`) >= DATE_SUB(CAST(CURRENT_TIMESTAMP as datetime), INTERVAL 35 DAY))"
                );
                break;
        }
    }
}
