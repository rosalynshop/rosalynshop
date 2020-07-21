<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */

namespace Amasty\XmlSitemap\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Actions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['generate'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'amxmlsitemap/sitemap/run',
                        [
                            'id' => $item['id']
                        ]
                    ),
                    'label' => __('Generate'),
                    'hidden' => false,
                ];
                $item[$this->getData('name')]['duplicate'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'amxmlsitemap/sitemap/duplicate',
                        [
                            'id' => $item['id']
                        ]
                    ),
                    'label' => __('Duplicate'),
                    'hidden' => false,
                ];
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'amxmlsitemap/sitemap/edit',
                        [
                            'id' => $item['id']
                        ]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
