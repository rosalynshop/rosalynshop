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



namespace Mirasvit\Report\Model\Export;

use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Exception\LocalizedException;
use Mirasvit\Report\Model\Context;
use Magento\Framework\ObjectManagerInterface;

class ConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{
    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();

        $name = md5(microtime());
        $file = 'export/' . $component->getName() . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();

        /** @var \Mirasvit\Report\Ui\DataProvider $dataProvider */
        $dataProvider = $component->getContext()->getDataProvider();

        /** @var \Mirasvit\ReportApi\Api\ResponseInterface $response */
        $response = $dataProvider->getResponse();

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $header = [];
        foreach ($response->getColumns() as $column) {
            $header[] = $column->getLabel();
        }
        $stream->writeCsv($header);

        foreach ($response->getItems() as $item) {
            $stream->writeCsv($item->getFormattedData());
        }

        $stream->writeCsv($response->getTotals()->getFormattedData());

        $stream->unlock();
        $stream->close();

        return [
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true  // can delete file after use
        ];
    }
}
