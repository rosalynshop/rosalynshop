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



namespace Mirasvit\Report\Ui\Report\Settings;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Report\Api\Data\ReportInterface;
use Mirasvit\Report\Api\Service\ColumnManagerInterface;
use Mirasvit\ReportApi\Api\SchemaInterface;
use Mirasvit\Report\Ui\Context;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Context
     */
    private $uiContext;

    /**
     * @var SchemaInterface
     */
    private $schema;
    /**
     * @var ColumnManagerInterface
     */
    private $columnManager;
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var ReportInterface $report
     */
    private $report;

    public function __construct(
        ArrayManager $arrayManager,
        ColumnManagerInterface $columnManager,
        Context $uiContext,
        SchemaInterface $schema,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->schema = $schema;
        $this->uiContext = $uiContext;
        $this->columnManager = $columnManager;
        $this->arrayManager = $arrayManager;

        $this->report = $uiContext->getReport();
        $this->report->init();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->arrayManager->set(
            'general/children/columns/arguments/data/config/columns',
            $meta,
            $this->getColumns()
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [$this->report->getIdentifier() => [
            'report'  => $this->report->getIdentifier(),
        ]];

        return $result;
    }

    /**
     * @return array
     */
    private function getColumns()
    {
        $offColumns    = array_unique(array_merge($this->report->getDimensions(), $this->report->getFastFilters()));
        $activeColumns = $this->columnManager->getActiveColumns($this->report);

        $columns = [];
        foreach ($this->report->getAllColumns() as $id) {
            $column = $this->schema->getColumn($id);
            if ($column->getLabel()) {
                $columns[] = [
                    'value'       => $id,
                    'label'       => $column->getLabel(),
                    'table'       => $column->getTable()->getLabel(),
                    'aggregator'  => $column->getAggregator()->getType(),
                    'type'        => $column->getType()->getType(),
                    'filter_only' => $column->isFilterOnly($this->report),
                    'active'      => in_array($id, $activeColumns, true),
                    'disabled'    => in_array($id, $offColumns, true)
                ];
            }
        }

        return $columns;
    }
}
