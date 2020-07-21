<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Source\Hreflang;

class Xdefault implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
      \Magento\Framework\App\RequestInterface $request,
      \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $currentWebsite = $this->request->getParam('website');
        if ($currentWebsite) {
            $stores = $this->storeManager->getWebsite($currentWebsite)->getStores();
        } else {
            $stores = $this->storeManager->getStores();
        }

        $options = [];
        foreach ($stores as $store) {
            /** @var \Magento\Store\Model\Store $store */
            $websiteId = $store->getWebsite()->getId();
            $storeId = $store->getStoreId();
            $label = $store->getName();
            if (!$currentWebsite) {
                $label = $store->getWebsite()->getName() . " — " . $label;
            }

            $options[] = [
                'label' => $label,
                'value' => $storeId,
                'website_id' => $websiteId,
            ];
        }

        usort($options, array($this, 'compare'));
        array_unshift(
            $options,
            ['value' => '', 'label' => __('--Please Select--')]
        );

        return $options;
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    private function compare(array $a, array $b)
    {
        if ($a['website_id'] === $b['website_id']) {
            return ($a['value'] < $b['value']) ? -1 : 1;
        }

        return ($a['website_id'] < $b['website_id']) ? -1 : 1;
    }
}
