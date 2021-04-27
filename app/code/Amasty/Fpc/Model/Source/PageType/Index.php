<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Index extends AbstractPage
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        $isMultiStoreMode = false,
        array $stores = []
    ) {
        parent::__construct($isMultiStoreMode, $stores);
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $limit
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllPages($limit = 0)
    {
        $result = [];

        foreach ($this->stores as $storeId) {
            try {
                $store = $this->storeManager->getStore($storeId)->getBaseUrl();
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $result [] = [
                'url' => $store,
                'store' => $storeId
            ];

            if (--$limit == 0) {
                break;
            }
        }

        return $result;
    }
}
