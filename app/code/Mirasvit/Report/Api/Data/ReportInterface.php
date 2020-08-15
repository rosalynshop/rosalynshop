<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report
 * @version   1.3.44
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Api\Data;

use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;
use Mirasvit\Report\Model\ChartConfig;
use Mirasvit\Report\Model\GridConfig;

interface ReportInterface
{
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return $this
     */
    public function init();

    /**
     * @return string
     */
    public function getTable();

    /**
     * @param string $tableName
     * @return $this
     */
    public function setTable($tableName);

    /**
     * @return string[]
     */
    public function getFastFilters();

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function addFastFilters($columnNames);

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function setFastFilters($columnNames);

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function addAvailableFilters($columnNames);

    /**
     * @return string[]
     */
    public function getAvailableFilters();

    /**
     * @return string[]
     */
    public function getDefaultColumns();

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function addDefaultColumns($columnNames);

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function setDefaultColumns($columnNames);

    /**
     * @return string[]
     */
    public function getAllColumns();

    /**
     * Provide all used by default columns: default, dimension and fast filter columns.
     *
     * @return string[]
     */
    public function getBaseColumns();

    /**
     * @return string[]
     */
    public function getColumns();

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function addColumns($columnNames);

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function setColumns($columnNames);

    /**
     * @return string
     */
    public function getDefaultDimension();

    /**
     * @param string $columnName
     * @return $this
     */
    public function setDefaultDimension($columnName);

    /**
     * @return string[]
     */
    public function getDimensions();

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function addDimensions($columnNames);

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function setDimensions($columnNames);

    /**
     * @return string[]
     */
    public function getRequiredColumns();

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function addRequiredColumns($columnNames);

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function setRequiredColumns($columnNames);

    /**
     * @return array
     */
    public function getDefaultFilters();

    /**
     * @param string[] $filters
     * @return $this
     */
    public function setDefaultFilters(array $filters);

    /**
     * @return GridConfig
     */
    public function getGridConfig();

    /**
     * @return ChartConfig
     */
    public function getChartConfig();

    /**
     * @param string|ColumnInterface $column
     * @param string|int $value
     * @param array $row
     * @return string
     */
    public function prepareValue($column, $value, $row);
}