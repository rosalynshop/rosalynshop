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



namespace Mirasvit\Report\Console\Command;

use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Report\Api\Data\ReportInterface;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;
use Mirasvit\ReportApi\Api\Processor\ResponseItemInterface;
use Mirasvit\ReportApi\Api\RequestBuilderInterface;
use Mirasvit\ReportApi\Api\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;

class TestCommand extends Command
{
    private $reportRepository;

    private $requestBuilder;

    private $objectManager;

    private $appState;

    private $totals = [];

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        RequestBuilderInterface $requestBuilder,
        ObjectManagerInterface $objectManager,
        State $appState
    ) {
        $this->reportRepository = $reportRepository;
        $this->requestBuilder = $requestBuilder;
        $this->objectManager = $objectManager;
        $this->appState = $appState;

        parent::__construct();
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:report:test')
            ->setDescription('For testing purpose')
            ->setDefinition([]);

        $this->addArgument('report', InputArgument::OPTIONAL);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('adminhtml');

        $reports = array_filter(explode(',', $input->getArgument('report')));

        foreach ($this->reportRepository->getList() as $report) {
            if ($reports
                && !in_array($report->getIdentifier(), $reports)) {
                continue;
            }

            if (!$report->getName()) {
                continue;
            }

            $output->writeln("<info>{$report->getIdentifier()}</info>");

            $report->init();

            foreach ($report->getDimensions() as $dimensionColumnName) {
                $output->writeln("<info>{$report->getIdentifier()} / $dimensionColumnName</info>");

                $columns = array_merge_recursive([$dimensionColumnName], $report->getDefaultColumns());
                $this->processRequest($output, $report, $columns, $dimensionColumnName);

                $columns = array_merge_recursive([$dimensionColumnName], $report->getColumns());

                shuffle($columns);
                $chunks = array_chunk($columns, 30);

                foreach ($chunks as $chunk) {
                    $this->processRequest($output, $report, $chunk, $dimensionColumnName);
                }
            }
        }
    }

    private function processRequest(OutputInterface $output, ReportInterface $report, $columns, $dimension)
    {
        $request = $this->requestBuilder->create()
            ->setTable($report->getTable())
            ->setDimension($dimension);

        $request->addColumn($dimension);

        foreach ($columns as $columnName) {
            $request->addColumn($columnName);
        }

        try {
            $ts = microtime(true);
            $response = $request->process();
            $time = microtime(true) - $ts;

            $this->renderResponse($output, $response, $time);
        } catch (\Exception $e) {
            throw new \Exception($request, 0, $e);
        }
    }

    private function renderResponse(OutputInterface $output, ResponseInterface $response, $time)
    {
        $output->writeln("Size: <comment>{$response->getSize()}</comment>");
        $output->writeln("Time: <comment>{$time}</comment>");

        if (count($response->getColumns()) <= 12) {
            $limit = 10;
            $headers = [];
            foreach ($response->getColumns() as $column) {
                $headers[] = $column->getLabel();
            }

            $table = new Table($output);
            $table->setHeaders($headers);
            foreach ($response->getItems() as $item) {
                $table->addRow($item->getFormattedData());
                if ($limit-- <= 0) {
                    break;
                }
            }

            $table->addRow($response->getTotals()->getFormattedData());
            $table->render();
        } else {
            $limit = 5;
            $table = new Table($output);

            $rows = [];
            $idx = 0;

            foreach ($response->getTotals()->getFormattedData() as $value) {
                $column = $response->getColumns()[$idx];
                $rows[$idx][] = $column->getLabel();
                $idx++;
            }

            foreach ($response->getItems() as $item) {
                $idx = 0;
                foreach ($item->getFormattedData() as $value) {
                    $rows[$idx][] = substr($value, 0, 10);
                    $idx++;
                }
                if ($limit-- <= 0) {
                    break;
                }
            }

            $idx = 0;
            foreach ($response->getTotals()->getFormattedData() as $value) {
                $rows[$idx][] = $value;
                $idx++;
            }
            $table->addRows($rows);
            $table->render();
            $output->writeln(str_repeat('-', 10));
        }

        $this->validate($response, $output);
    }

    private function validate(ResponseInterface $response, OutputInterface $output)
    {
        foreach ($response->getTotals()->getFormattedData() as $column => $value) {
            $key = $column;

            if (strpos($value, '%') !== false) {
                continue;
            }

            if ($value === null) {
                continue;
            }

            if (strpos($column, '__sum') === false
                && strpos($column, '__cnt') === false
                && strpos($column, '__avg') === false) {
                continue;
            }

            if (!isset($this->totals[$key])) {
                $this->totals[$key] = $value;
                $output->writeln("Set totals $key: <info>$value</info>");
            } else {
                if ($this->totals[$key] != $value) {
                    throw new \Exception("Wrong totals $key. Expected: {$this->totals[$key]}, Actual: $value");
                } else {
                    $output->writeln("Match totals $key: <info>$value</info>");
                }
            }
        }
    }
}
