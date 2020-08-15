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



namespace Mirasvit\Report\Ui\Component\Listing;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns as UiColumns;
use Mirasvit\Report\Ui\Component\ColumnFactory;
use Mirasvit\Report\Ui\Component\Listing\Column\ActionsFactory as ColumnActionsFactory;
use Mirasvit\Report\Ui\Context;
use Mirasvit\ReportApi\Api\SchemaInterface;

class Columns extends UiColumns
{
    /**
     * @var Context
     */
    private $uiContext;

    /**
     * @var ColumnFactory
     */
    private $columnFactory;

    /**
     * @var ColumnActionsFactory
     */
    private $columnActionsFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    private $schema;

    public function __construct(
        SchemaInterface $schema,
        RequestInterface $request,
        ContextInterface $context,
        Context $uiContext,
        ColumnActionsFactory $columnActionsFactory,
        ColumnFactory $columnFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);

        $this->schema = $schema;
        $this->request = $request;
        $this->uiContext = $uiContext;
        $this->columnFactory = $columnFactory;
        $this->columnActionsFactory = $columnActionsFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function prepare()
    {
        $this->components = [];

        parent::prepare();

        $columns    = [];
        $gridConfig = $this->uiContext->getReport()->getGridConfig();
        $allColumns = $this->uiContext->getColumnManager()->getActiveColumns($this->uiContext->getReport());

        foreach ($allColumns as $identifier) {
            $column = $this->schema->getColumn($identifier);

            $columns[$column->getIdentifier()] = [
                'label'        => (string)$column->getLabel(),
                'table'        => (string)$column->getTable()->getLabel(),
                'type'         => $column->getType()->getJsType(),
                'options'      => method_exists($column->getType(), 'getOptions')
                    ? $column->getType()->getOptions()
                    : false,
                'visible'      => in_array($identifier, $this->uiContext->getReport()->getDefaultColumns()),
                'filter'       => $column->getType()->getJsFilterType(),
                'add_field'    => false,
                'sorting'      => ($identifier == $gridConfig->getOrderColumn())
                    ? $gridConfig->getOrderDirection()
                    : false,
                // column hidden by class OR exists only in filters
                'isFilterOnly' => $column->isFilterOnly($this->uiContext->getReport()),
                'isDimension'  => in_array($identifier, $this->uiContext->getReport()->getDimensions()),
            ];
        }

        $columnSortOrder = 100000;

        foreach ($columns as $identifier => $data) {
            if (!isset($this->components[$identifier])) {
                $columnSortOrder -= 10;
                $config = $data;
                $config['sortOrder'] = $columnSortOrder;

                if ($this->context->getRequestParam('columns')
                    && in_array($identifier, $this->context->getRequestParam('columns'))) {
                    $config['add_field'] = true;
                }

                /** @var \Magento\Ui\Component\Listing\Columns\Column $column */
                $column = $this->columnFactory->create($identifier, $this->getContext(), $config);

                if ($config['add_field'] || $config['isDimension']
                    || array_key_exists($identifier, $this->request->getParam('filters', []))) {
                    $column->prepare();
                }

                $this->addComponent($identifier, $column);
            }
        }

        $this->addActionsColumn();
    }

    /**
     * Add actions column
     *
     * @return void
     */
    private function addActionsColumn()
    {
        if ($this->uiContext->getReport()->hasActions()) {
            $arguments = [
                'data'    => [
                    'js_config' => [
                        'component' => 'Magento_Ui/js/grid/columns/actions',
                    ],
                    'config'    => [
                        'label'     => __('Actions'),
                        'dataType'  => 'actions',
                        'sortOrder' => 0,
                    ],
                    'name'      => 'actions',
                ],
                'context' => $this->context,
            ];


            $actions = $this->columnActionsFactory->create($arguments);
            $actions->prepare();

            $this->addComponent('actions', $actions);
        }
    }
}
