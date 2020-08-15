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



namespace Mirasvit\Report\Repository\Email;

use Mirasvit\Report\Api\Repository\Email\BlockRepositoryInterface;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponentFactoryFactory;
use Magento\Framework\Registry;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\Component\Listing\Columns;
use Magento\Ui\Api\BookmarkManagementInterface;
use Mirasvit\Report\Api\Service\DateServiceInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Mirasvit\ReportApi\Api\RequestBuilderInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultRepository implements BlockRepositoryInterface
{
    /**
     * @var UiComponentFactoryFactory
     */
    protected $uiComponentFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ReportRepositoryInterface
     */
    protected $reportRepository;

    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var BookmarkManagementInterface
     */
    protected $bookmarkManagement;

    /**
     * @var DateServiceInterface
     */
    protected $dateService;

    /**
     * @var AbstractReport
     */
    protected $report;

    /**
     * @var BookmarkInterface
     */
    protected $bookmark;

    public function __construct(
        RequestBuilderInterface $requestBuilder,
        ReportRepositoryInterface $reportRepository,
        UiComponentFactoryFactory $uiComponentFactory,
        Registry $registry,
        MetadataProvider $metadataProvider,
        RequestInterface $request,
        BookmarkManagementInterface $bookmarkManagement,
        DateServiceInterface $dateService,
        PricingHelper $pricingHelper
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->uiComponentFactory = $uiComponentFactory;
        $this->registry = $registry;
        $this->reportRepository = $reportRepository;
        $this->metadataProvider = $metadataProvider;
        $this->request = $request;
        $this->bookmarkManagement = $bookmarkManagement;
        $this->dateService = $dateService;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        $blocks = [];
        foreach ($this->reportRepository->getList() as $report) {
            if ($report->getName()) {
                $blocks[$report->getIdentifier()] = __('Report: %1', $report->getName());
            }
        }

        return $blocks;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getContent($identifier, $data)
    {
        return $this->build($data);
    }

    /**
     * @param array $reportData
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function build(array $reportData)
    {
        $reportIdentifier = $reportData['identifier'];
        $report = $this->reportRepository->get($reportIdentifier);
        $interval = $this->dateService->getInterval($reportData['timeRange']);
        $request = $this->requestBuilder->create()
            ->setTable($report->getTable())
            ->setDimension($report->getDefaultDimension())
            ->setPageSize((isset($reportData['limit']) && $reportData['limit']) ? $reportData['limit'] : 100)
            ->addFilter('created_at', $interval->getFrom()->toString(\Zend_Date::W3C), 'gteq', 'A')
            ->addFilter('created_at', $interval->getTo()->toString(\Zend_Date::W3C), 'lteq', 'A');

        $request->addColumn($report->getDefaultDimension());

        foreach ($report->getDefaultColumns() as $column) {
            $request->addColumn($column);
        }

        $response = $request->process();

        $rows = [];
        foreach ($response->getColumns() as $column) {
            $rows['header'][] = $column->getLabel();
        }


        foreach ($response->getItems() as $idx => $item) {
            foreach ($item->getFormattedData() as $key => $value) {
                $rows[$idx][] = $value;
            }
        }

        foreach ($response->getTotals()->getFormattedData() as $key => $value) {
            $rows['footer'][] = $value;
        }

        $table = '<table>';
        foreach ($rows as $idx => $row) {
            $table .= '<tr>';
            foreach ($row as $column) {
                if ($idx === 'header' || $idx === 'footer') {
                    $table .= '<th>' . $column . '</th>';
                } else {
                    $table .= '<td>' . $column . '</td>';
                }
            }
            $table .= '</tr>';
        }

        $table .= '</table>';

        $name = $report->getName();

        return "
            <h2>{$name}</h2>
            <div class='interval'>{$this->dateService->getIntervalHint($reportData['timeRange'])}</div>
            
            <div class='table-wrapper'>$table</div>
        ";
    }
}
