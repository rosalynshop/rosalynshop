<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class HistoryActions extends Column
{
    const HISTORY_PROVIDER = 'amaudit_history_listing.amaudit_history_listing_data_source';

    const LOG_PROVIDER = 'amaudit_actionslog_listing.amaudit_actionslog_listing_data_source';

    /**
     * @var array
     */
    private $previewTypes = [
        'Edit',
        'New',
        'Restore'
    ];

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $provider = self::LOG_PROVIDER;
        $jsConfig = $this->getJsConfig($this);

        if (isset($jsConfig['extends']) && $jsConfig['extends'] == 'amaudit_history_listing') {
            $provider = self::HISTORY_PROVIDER;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')]['details'] = [
                'href' => $this->context->getUrl(
                    'amaudit/actionslog/edit',
                    ['id' => $item['id']]
                ),
                'label' => __('View Details'),
                'hidden' => false,
            ];

            if (in_array($item['type'], $this->previewTypes)) {
                $item[$this->getData('name')]['preview'] = [
                    'callback' => [
                        'target' => 'open',
                        'provider' => $provider,
                        'params' => [
                            $this->context->getUrl('amaudit/actionslog/preview'),
                            $item['id']
                        ],
                    ],
                    'label' => __('Preview Details'),
                    'hidden' => false,
                ];
            }

            if (isset($item['type'])) {
                $item['type'] = '<span class="amaudit-' . $item['type'] . '">' . $item['type'] . '</span>';
            }
        }

        return $dataSource;
    }
}
