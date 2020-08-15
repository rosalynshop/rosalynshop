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

use Magento\Framework\Convert\Excel;
use Magento\Framework\Exception\LocalizedException;

class ConvertToXml extends \Magento\Ui\Model\Export\ConvertToXml
{
    /**
     * @param \Mirasvit\ReportApi\Processor\ResponseItem $item
     * @return array
     */
    public function getItemData($item)
    {
        return $item->getFormattedData();
    }

    /**
     * Returns XML file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getXmlFile()
    {
        $component = $this->filter->getComponent();

        $name = md5(microtime());
        $file = 'export/' . $component->getName() . $name . '.xml';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();

        /** @var \Mirasvit\Report\Ui\DataProvider $dataProvider */
        $dataProvider = $component->getContext()->getDataProvider();

        /** @var \Mirasvit\ReportApi\Api\ResponseInterface $response */
        $response = $dataProvider->getResponse();

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        /** @var Excel $excel */
        $excel = $this->excelFactory->create([
            'iterator'    => $this->iteratorFactory->create(['items' => $response->getItems()]),
            'rowCallback' => [$this, 'getItemData'],
        ]);

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $header = [];
        foreach ($response->getColumns() as $column) {
            $header[] = $column->getLabel();
        }
        $excel->setDataHeader($header);

        $excel->write($stream, $component->getName() . '.xml');

        $stream->unlock();
        $stream->close();

        return [
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true  // can delete file after use
        ];
        //
        //        $productMetadata = $this->objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        //        $version = $productMetadata->getVersion();
        //
        //        if (!(version_compare($version, '2.1.8', '>=') && version_compare($version, '2.2.0', '<'))) {
        //            $this->filter->applySelectionOnTargetProvider();
        //        }
        //
        //
        //        $component->getContext()->getDataProvider();
        //
        //        /** @var SearchResultInterface $searchResult */
        //        $searchResult = $component->getContext()->getDataProvider()->getResponse();
        //
        //        /** @var DocumentInterface[] $searchResultItems */
        //        $searchResultItems = $searchResult->getItems();
        //
        //        $this->prepareItems($component->getName(), $searchResultItems);
        //
        //        /** @var SearchResultIterator $searchResultIterator */
        //        $searchResultIterator = $this->iteratorFactory->create(['items' => $searchResultItems]);
        //
        //        /** @var Excel $excel */
        //        $excel = $this->excelFactory->create([
        //            'iterator'    => $searchResultIterator,
        //            'rowCallback' => [$this, 'getReportData'],
        //        ]);
        //
        //        $this->directory->create('export');
        //        $stream = $this->directory->openFile($file, 'w+');
        //        $stream->lock();
        //
        //        $searchResultIterator->rewind();
        //        $excel->setDataHeader($this->metadataProvider->getHeaders($component));
        //        $excel->write($stream, $component->getName() . '.xml');
        //
        //        $stream->unlock();
        //        $stream->close();
        //
        //        return [
        //            'type'  => 'filename',
        //            'value' => $file,
        //            'rm'    => true  // can delete file after use
        //        ];
    }
}
