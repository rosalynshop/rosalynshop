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
 * @package   mirasvit/module-report-api
 * @version   1.0.12
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Processor;

use Mirasvit\ReportApi\Api\Config\TypeInterface;
use Mirasvit\ReportApi\Api\RequestInterface;
use Mirasvit\ReportApi\Api\ResponseInterface;
use Mirasvit\ReportApi\Config\Schema;

class ResponseBuilder
{
    private $responseFactory;

    private $responseItemFactory;

    private $responseColumnFactory;

    private $schema;

    public function __construct(
        ResponseFactory $responseFactory,
        ResponseItemFactory $responseItemFactory,
        ResponseColumnFactory $responseColumnFactory,
        Schema $schema
    ) {
        $this->responseFactory = $responseFactory;
        $this->responseItemFactory = $responseItemFactory;
        $this->responseColumnFactory = $responseColumnFactory;
        $this->schema = $schema;
    }

    /**
     * @param RequestInterface $request
     * @param \Mirasvit\ReportApi\Handler\Collection[] $collections
     * @return ResponseInterface
     */
    public function create(RequestInterface $request, array $collections)
    {
        $groups = [];
        foreach (array_keys($collections) as $group) {
            $groups[$group] = [];
        }

        foreach ($collections as $group => $collection) {
            foreach ($collection as $data) {
                $pk = $this->getPk($request->getDimension(), $data, $groups[$group]);

                $groups[$group][$pk] = $data;
            }
        }

        $result = [];
        foreach ($groups['A'] as $pk => $data) {
            foreach ($groups as $group => $items) {
                if ($group != 'A') {
                    foreach ($items as $sPk => $itm) {
                        if ($pk == $sPk) {
                            foreach ($itm as $k => $v) {
                                $data["$group|$k"] = $v;
                            }
                        }
                    }
                }
            }
            $result[] = $this->responseItemFactory->create(['data' => [
                ResponseItem::DATA           => $data,
                ResponseItem::FORMATTED_DATA => $this->getFormattedData($data),
            ]]);
        }

        $columns = [];
        foreach ($request->getColumns() as $name) {
            $column = $this->schema->getColumn($name);
            $columns[] = $this->responseColumnFactory->create(['data' => [
                ResponseColumn::NAME  => $name,
                ResponseColumn::LABEL => $column->getLabel(),
                ResponseColumn::TYPE  => $column->getType()->getJsType(),
            ]]);
        }

        $totalsData = $collections['A']->getTotals();
        foreach ($collections as $group => $collection) {
            if ($group == 'A') {
                continue;
            }

            foreach ($collection->getTotals() as $k => $v) {
                $totalsData["$group|$k"] = $v;
            }
        }

        $data = [
            Response::SIZE    => $collections['A']->getSize(),
            Response::TOTALS  => $this->responseItemFactory->create(['data' => [
                ResponseItem::DATA           => $totalsData,
                ResponseItem::FORMATTED_DATA => $this->getFormattedData($totalsData, true),
            ]]),
            Response::ITEMS   => $result,
            Response::COLUMNS => $columns,
            Response::REQUEST => $request,
        ];

        // in some cases when result set contains only 1 row the totals may be empty
        // so we simply put the result items in totals
        if (!$totalsData && $data[Response::SIZE] == 1) {
            $data[Response::TOTALS] = reset($result);
        }

        $response = $this->responseFactory->create(
            ['data' => $data]
        );

        return $response;
    }

    private function getFormattedData($data, $isTotals = false)
    {
        $formattedData = [];
        foreach ($data as $name => $value) {
            $column = $this->schema->getColumn($name);

            $formattedData[$name] = $column->getType()->getFormattedValue($value, $column->getAggregator());

            if ($isTotals && $value === null) {
                $formattedData[$name] = null;
            }
        }

        return $formattedData;
    }

    private function getPk($dimension, $data, $items)
    {
        $dimensionColumn = $this->schema->getColumn($dimension);

        if (isset($data[$dimension])) {
            $pk = $dimensionColumn->getType()->getPk($data[$dimension], $dimensionColumn->getAggregator());
        } else {
            $pk = 0;
        }

        $idx = 0;
        while (isset($items["{$pk}_{$idx}"])) {
            $idx++;
        }

        return "{$pk}_{$idx}";
    }
}
