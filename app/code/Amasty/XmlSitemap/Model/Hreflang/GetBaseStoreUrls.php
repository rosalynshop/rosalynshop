<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

class GetBaseStoreUrls implements GetBaseStoreUrlsInterface
{
    /**
     * @var array|null
     */
    private $storeUrls;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function execute()
       {
           if ($this->storeUrls === null) {
               $this->storeUrls = [];
               foreach ($this->storeManager->getStores() as $storeId => $store) {
                   $this->storeUrls[$storeId] = rtrim($store->getBaseUrl(), '/') . '/';
               }
           }

           return $this->storeUrls;
       }
}
