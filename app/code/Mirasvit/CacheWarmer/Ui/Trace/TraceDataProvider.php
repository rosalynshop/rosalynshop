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
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Ui\Trace;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Mirasvit\CacheWarmer\Api\Data\TraceInterface;
use Mirasvit\CacheWarmer\Service\Rate\CacheFillRateService;

class TraceDataProvider extends DataProvider
{
    private $cacheFillRateService;

    public function __construct(
        CacheFillRateService $cacheFillRateService,

        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {

        $this->cacheFillRateService = $cacheFillRateService;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $result = [];

        $result['items'] = [];

        /** @var TraceInterface $trace */
        foreach ($searchResult->getItems() as $trace) {
            $itemData = [
                'id_field_name'             => TraceInterface::ID,
                TraceInterface::ID          => $trace->getId(),
                TraceInterface::STARTED_AT  => $trace->getStartedAt(),
                TraceInterface::FINISHED_AT => $trace->getFinishedAt(),
                TraceInterface::CREATED_AT  => $trace->getCreatedAt(),
            ];

            $shortTrace = $trace->getTrace();
            if (isset($shortTrace['backtrace'])) {
                unset($shortTrace['backtrace']);
            }
            $itemData['trace'] = $this->arrayToTable('_trace', $shortTrace);
            $itemData['full_trace'] = $this->arrayToTable('_trace', $trace->getTrace());
            $itemData['fill_rate'] = $this->getFillRateDegradation($trace);
            $result['items'][] = $itemData;
        }

        $result['totalRecords'] = $searchResult->getTotalCount();

        return $result;
    }

    private function getFillRateDegradation(TraceInterface $trace)
    {
        $threshold = 2;
        $history   = $this->cacheFillRateService->getHistory();

        $tsFrom = ceil(strtotime($trace->getStartedAt()) / 60) * 60 - $threshold * 60;
        $tsTo   = ceil(strtotime($trace->getFinishedAt()) / 60) * 60 + 60;

        $beforeRateSum = 0;
        $beforeRateCnt = 0;

        $afterRateSum = 0;
        $afterRateCnt = 0;

        for ($i = 0; $i < $threshold; $i++) {
            $from = $tsFrom + $i * 60;
            if (isset($history[$from])) {
                $beforeRateSum = $history[$from];
                $beforeRateCnt++;
            }

            $to = $tsTo + $i * 60;
            if (isset($history[$to])) {
                $afterRateSum = $history[$to];
                $afterRateCnt++;
            }
        }


        if (!$beforeRateCnt || !$afterRateCnt) {
            return "Waiting for data..";
        }

        $before = $beforeRateSum / $beforeRateCnt;
        $after  = $afterRateSum / $afterRateCnt;

        return round($before)."% => ".round($after)."%";
    }

    private function arrayToTable($class, $data)
    {
        $html = '';

        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (isset($value[0])) {
                    $value = implode(', ', $value);
                } else {
                    $value = $this->arrayToTable($class, $value);
                }
            }

            if ($value) {
                $c    = '_' . preg_replace("/[^A-Z]/i", '', strtolower($key));
                $html .= "<tr class='$c'>";
                $html .= "<td>$key</td>";
                $html .= "<td>$value</td>";
                $html .= "</tr>";
            }
        }

        if ($html) {
            return "<div class='mst-cache-warmer__job-listing-array $class'><table>$html</table></div>";
        }

        return '';
    }
}
