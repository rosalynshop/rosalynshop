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



namespace Mirasvit\Report\Ui;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\ReportApi\Api\Config\TypeInterface;
use Mirasvit\ReportApi\Api\Processor\ResponseItemInterface;
use Mirasvit\ReportApi\Api\SchemaInterface;
use Mirasvit\ReportApi\Api\RequestInterface;
use Mirasvit\ReportApi\Processor\RequestBuilder;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Context
     */
    private $uiContext;

    /**
     * @var SchemaInterface
     */
    private $provider;

    /**
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        Context $uiContext,
        SchemaInterface $provider,
        RequestBuilder $requestBuilder,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->provider = $provider;
        $this->uiContext = $uiContext;

        $this->requestBuilder = $requestBuilder;

        $this->init();
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->request = $this->requestBuilder->create();

        $this->request->setTable($this->uiContext->getReport()->getTable());

        foreach ($this->uiContext->getReport()->getRequiredColumns() as $column) {
            $this->request->addColumn($column);
        }

        foreach ($this->uiContext->getReport()->getDefaultFilters() as $filter) {
            $this->request->addFilter(
                $filter[0], $filter[1], $filter[2]
            );
        }
    }

    /**
     * @param ResponseItemInterface[] $items
     * @return array
     */
    private function map($items)
    {
        $result = [];
        foreach ($items as $key => $item) {
            foreach ($item->getData() as $code => $value) {

                $column = $this->provider->getColumn($code);

                $result[$key][$code . '_orig'] = $value;

                $result[$key][$code] = $column->getType()->getJsType() == TypeInterface::JS_TYPE_SELECT
                    ? $value
                    : $item->getFormattedData($code);
            }
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getData()
    {
        $startTime = microtime(true);

        $response = $this->getResponse();
        $comparisonResponse = null;

        $items = $this->map($response->getItems());

        $totals = $this->map([$response->getTotals()]);
        $totals = array_shift($totals);

        $result = [
            'totalRecords'    => $response->getSize(),
            'items'           => array_values($items),
            'totals'          => $totals ? [$totals] : [],
            'dimensionColumn' => $this->uiContext->getActiveDimension(),
            'request'         => $response->getRequest()->toArray(),
            'dataTime'        => round(microtime(true) - $startTime, 4),
            'requestTime'     => round(microtime(true) - @$_SERVER['REQUEST_TIME_FLOAT'], 4),
        ];

        return $result;
    }

    /**
     * @return \Mirasvit\ReportApi\Api\ResponseInterface
     */
    public function getResponse()
    {
        return $this->request->process();
    }

    /**
     * {@inheritdoc}
     */
    public function addField($field, $alias = null)
    {
        $this->request->addColumn($field);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction)
    {
        $this->request->addSortOrder($field, $direction);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter, $group = '')
    {
        $this->request->addFilter(
            $filter->getField(),
            $filter->getValue(),
            $filter->getConditionType(),
            $group
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDimension($column)
    {
        $this->request->setDimension($column)
            ->addColumn($column);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($page, $size)
    {
        $this->request->setPageSize($size)
            ->setCurrentPage($page);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $this->data['config']['params'] = [
            'report' => $this->uiContext->getReport()->getIdentifier(),
            'id'     => $this->uiContext->getReport()->getId(),
        ];

        return isset($this->data['config']) ? $this->data['config'] : [];
    }
}
