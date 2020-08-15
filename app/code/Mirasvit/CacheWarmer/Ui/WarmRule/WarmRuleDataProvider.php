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



namespace Mirasvit\CacheWarmer\Ui\WarmRule;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Mirasvit\CacheWarmer\Api\Data\WarmRuleInterface;

class WarmRuleDataProvider extends DataProvider
{
    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $result = [];

        $result['items'] = [];

        /** @var WarmRuleInterface $WarmRule */
        foreach ($searchResult->getItems() as $WarmRule) {
            $itemData = [
                'id_field_name'             => WarmRuleInterface::ID,
                WarmRuleInterface::ID        => $WarmRule->getId(),
                WarmRuleInterface::NAME      => $WarmRule->getName(),
                WarmRuleInterface::IS_ACTIVE => $WarmRule->isActive(),
                WarmRuleInterface::PRIORITY  => $WarmRule->getPriority(),
            ];

            $headers = [];
            foreach ($WarmRule->getHeaders() as $header => $value) {
                $headers[] = "$header: $value";
            }
            $itemData['headers'] = implode(PHP_EOL, $headers);

            $varyData = [];
            foreach ($WarmRule->getVaryData() as $header => $value) {
                $varyData[] = "$header: $value";
            }
            $itemData['vary_data'] = implode(PHP_EOL, $varyData);

            $result[$WarmRule->getId()] = $itemData;
            $result['items'][]         = $itemData;
        }

        $result['totalRecords'] = $searchResult->getTotalCount();

        return $result;
    }
}
