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



namespace Mirasvit\Report\Model;

use Magento\Backend\Block\Widget\Tab;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\DataObject;
use Mirasvit\Report\Api\Data\ReportInterface;
use Mirasvit\Report\Ui\Context as UiContext;
use Mirasvit\ReportApi\Api\Config\AggregatorInterface;
use Mirasvit\ReportApi\Api\Config\ColumnInterface;
use Mirasvit\ReportApi\Api\Config\TableInterface;
use Mirasvit\ReportApi\Api\Config\TypeInterface;

abstract class AbstractReport extends AbstractSimpleObject implements ReportInterface
{
    const ID = 'id';

    const TABLE = 'table';

    const FAST_FILTERS = 'fast_filters';

    const COLUMNS = 'columns';
    const DEFAULT_COLUMNS = 'default_columns';
    const REQUIRED_COLUMNS = 'required_columns';

    const DIMENSIONS = 'dimensions';
    const DEFAULT_DIMENSION = 'default_dimension';

    const DEFAULT_FILTERS = 'default_filters';
    const AVAILABLE_FILTERS = 'available_filters';

    const GRID_CONFIG = 'grid_config';
    const CHART_CONFIG = 'chart_config';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Mirasvit\ReportApi\Api\SchemaInterface
     */
    protected $provider;

    /**
     * @var UiContext
     */
    private $uiContext;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
        $this->provider = $this->context->getProvider();

        parent::__construct([
            self::FAST_FILTERS      => [],
            self::DEFAULT_COLUMNS   => [],
            self::REQUIRED_COLUMNS  => [],
            self::COLUMNS           => [],
            self::DIMENSIONS        => [],
            self::DEFAULT_FILTERS   => [],
            self::AVAILABLE_FILTERS => [],
            self::GRID_CONFIG       => new GridConfig(),
            self::CHART_CONFIG      => new ChartConfig(),
        ]);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        $code = str_replace('Mirasvit\Reports\Reports\\', '', get_class($this));

        return strtolower(str_replace(['\Interceptor', '\\'], ['', '_'], $code));
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @param \Mirasvit\Report\Ui\Context $context
     * @return $this
     */
    public function setUiContext($context)
    {
        $this->uiContext = $context;

        return $this;
    }

    /**
     * @return UiContext
     */
    public function getUiContext()
    {
        return $this->uiContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return $this->_get(self::TABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTable($tableName)
    {
        return $this->setData(self::TABLE, $tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function getFastFilters()
    {
        return $this->_get(self::FAST_FILTERS);
    }

    /**
     * {@inheritdoc}
     */
    public function addFastFilters($columnNames)
    {
        return $this->addData(self::FAST_FILTERS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function setFastFilters($columnNames)
    {
        return $this->setData(self::FAST_FILTERS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultColumns()
    {
        return $this->_get(self::DEFAULT_COLUMNS);
    }

    /**
     * {@inheritdoc}
     */
    public function addDefaultColumns($columnNames)
    {
        $this->addColumns($columnNames);

        return $this->addData(self::DEFAULT_COLUMNS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultColumns($columnNames)
    {
        $this->addColumns($columnNames);

        return $this->setData(self::DEFAULT_COLUMNS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllColumns()
    {
        return array_unique(array_merge(
            $this->getDefaultColumns(),
            $this->getRequiredColumns(),
            $this->getColumns(),
            $this->getDimensions(),
            $this->getAvailableFilters(),
            $this->getFastFilters()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseColumns()
    {
        return array_unique(array_merge(
            $this->getDefaultColumns(),
            $this->getDimensions(),
            $this->getFastFilters(),
            $this->getRequiredColumns()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->_get(self::COLUMNS);
    }

    /**
     * {@inheritdoc}
     */
    public function addColumns($columnNames)
    {
        return $this->addData(self::COLUMNS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function setColumns($columnNames)
    {
        return $this->setData(self::COLUMNS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultDimension()
    {
        return $this->_get(self::DEFAULT_DIMENSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultDimension($columnName)
    {
        $this->addDimensions([$columnName]);

        $dimension = $this->provider->getColumn($columnName);

        $this->addColumns($this->getColumnsToSelect($dimension));
        $this->addAvailableFilters($this->getColumnsToFilter($dimension));

        return $this->setData(self::DEFAULT_DIMENSION, $columnName);
    }

    private function getColumnsToSelect(ColumnInterface $dimension)
    {
        $columns = [];

        if ($dimension->isUnique()) {
            $columns = array_merge_recursive($columns, $this->getByAggregatorType($dimension->getTable(), 'simple'));

            foreach ($this->provider->getRelations() as $relation) {
                if ($relation->getOppositeTable($dimension->getTable())
                    && in_array($relation->getType($dimension->getTable()), ['11', '1n'])) {
                    $columns = array_merge_recursive(
                        $columns,
                        $this->getByAggregatorType($relation->getOppositeTable($dimension->getTable()), 'simple')
                    );
                }
            }
        } else {
            $columns = array_merge_recursive($columns, $this->getByAggregatorType($dimension->getTable(), 'complex'));

            foreach ($this->provider->getRelations() as $relation) {
                if ($relation->getOppositeTable($dimension->getTable())
                    && $relation->getType($dimension->getTable()) == '11') {
                    $columns = array_merge_recursive(
                        $columns,
                        $this->getByAggregatorType($relation->getOppositeTable($dimension->getTable()), 'complex')
                    );
                }
            }
        }

        return $columns;
    }

    private function getColumnsToFilter(ColumnInterface $dimension)
    {
        $columns = [];

        if ($dimension->isUnique()) {

        } else {
            $columns = array_merge_recursive($columns, $this->getByAggregatorType($dimension->getTable(), 'simple'));
        }

        return $columns;
    }

    private function getByAggregatorType(TableInterface $table, $type)
    {
        $result = [];
        foreach ($table->getColumns() as $column) {
            if ($this->getAggregatorType($column->getAggregator()) == $type) {
                if ($column->getLabel()
                    && !$column->isInternal()
                    && $column->getAggregator()->getType() != AggregatorInterface::TYPE_CONCAT) {
                    $result[] = $column->getIdentifier();
                }
            }
        }

        return $result;
    }

    private function getAggregatorType(AggregatorInterface $aggregator)
    {
        return in_array($aggregator->getType(), [
            AggregatorInterface::TYPE_SUM,
            AggregatorInterface::TYPE_COUNT,
            AggregatorInterface::TYPE_AVERAGE,
            AggregatorInterface::TYPE_CONCAT,
        ]) ? 'complex' : 'simple';
    }

    /**
     * {@inheritdoc}
     */
    public function getDimensions()
    {
        return $this->_get(self::DIMENSIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function addDimensions($columnNames)
    {
        return $this->addData(self::DIMENSIONS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function setDimensions($columnNames)
    {
        return $this->setData(self::DIMENSIONS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function addAvailableFilters($columnNames)
    {
        return $this->addData(self::AVAILABLE_FILTERS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableFilters()
    {
        return $this->_get(self::AVAILABLE_FILTERS);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredColumns()
    {
        return $this->_get(self::REQUIRED_COLUMNS);
    }

    /**
     * {@inheritdoc}
     */
    public function addRequiredColumns($columnNames)
    {
        return $this->addData(self::REQUIRED_COLUMNS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequiredColumns($columnNames)
    {
        return $this->setData(self::REQUIRED_COLUMNS, $columnNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFilters()
    {
        return $this->_get(self::DEFAULT_FILTERS);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultFilters(array $filters)
    {
        return $this->setData(self::DEFAULT_FILTERS, $filters);
    }

    /**
     * @return \Mirasvit\Report\Model\GridConfig
     */
    public function getGridConfig()
    {
        return $this->_get(self::GRID_CONFIG);
    }

    /**
     * @return \Mirasvit\Report\Model\ChartConfig
     */
    public function getChartConfig()
    {
        return $this->_get(self::CHART_CONFIG);
    }

    /**
     * @param string $key
     * @param string|array $data
     * @return $this
     */
    public function addData($key, $data)
    {
        return $this->setData($key, array_unique(array_merge_recursive(
            $this->_get($key),
            $data
        )));
    }

    /**
     * @return array
     */
    public function getActions($item)
    {
        return [];
    }

    /**
     * @return bool
     */
    public function hasActions()
    {
        $reflector = new \ReflectionMethod($this, 'getActions');
        $isProto = ($reflector->getDeclaringClass()->getName() !== get_class($this));

        return !$isProto;
    }

    /**
     * @param Column $column
     * @param string $value
     * @param array $row
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepareValue($column, $value, $row)
    {
        return $value;
    }

    /**
     * @param string $report
     * @param array $filters
     * @return string
     */
    public function getReportUrl($report, $filters = [])
    {
        return $this->context->urlManager->getUrl(
            'reports/report/view',
            [
                'report' => $report,
                '_query' => [
                    'filters' => $filters,
                ],
            ]
        );
    }
}
