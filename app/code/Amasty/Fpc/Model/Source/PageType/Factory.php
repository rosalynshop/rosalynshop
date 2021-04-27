<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Amasty\Fpc\Model\Config;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $type
     * @param array $params
     *
     * @return mixed
     */
    public function create($type, $params = [])
    {
        $stores = $this->config->getStores();

        if (count($stores) <= 1) {
            $isMultiStoreMode = false;
        } else {
            $baseUrls = array_map(function ($storeId) {
                return $this->storeManager->getStore($storeId)->getBaseUrl();
            }, $stores);
            $isMultiStoreMode = count(array_unique($baseUrls)) > 1 ? true : false;
        }

        if (!$isMultiStoreMode || !$stores) {
            $stores = [null];
        }

        $params = array_merge([
            'isMultiStoreMode' => $isMultiStoreMode,
            'stores' => $stores
        ], $params);

        return $this->objectManager->create('\Amasty\Fpc\Model\Source\PageType\\' . ucfirst($type), $params);
    }
}
