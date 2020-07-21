<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Ui\Component\Listing\Columns;

use Magento\Store\Ui\Component\Listing\Column\Store;

class StoreView extends Store
{
    /**
     * @param array $dataSource
     *
     * @return array $dataSource
     */
    public function prepareDataSource(array $dataSource)
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (isset($item['store_id'])) {
                $storeId = (int)$item['store_id'];
                $item['store_id'] = [];
                $item['store_id'][] = $storeId;
            }
        }

        $dataSource = parent::prepareDataSource($dataSource);

        return $dataSource;
    }
}
