<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Url extends Column
{

    /**
     * @var \Amasty\XmlSitemap\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * Url constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Amasty\XmlSitemap\Helper\Data $helper
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Amasty\XmlSitemap\Helper\Data $helper,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Filesystem $filesystem,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->helper = $helper;
        $this->ioFile = $ioFile;
        $this->filesystem = $filesystem;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as $key => $item) {
            $filePath = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath()
                . $item['folder_name'];
            if ($this->ioFile->fileExists($filePath)) {
                $url = $this->helper->getCorrectUrl($item['folder_name'], $item['store_id']);
                $dataSource['data']['items'][$key]['result_link'] = sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    $url,
                    $url
                );
            }
        }

        return $dataSource;
    }
}
