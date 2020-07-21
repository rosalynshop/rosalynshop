<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Observer\Catalog\Product;

use Magento\Framework\Event\ObserverInterface;

class BeforeSave implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $urlKey  = trim($product->getUrlKey());
        $product->setNeedUpdateProductUrl(empty($urlKey));
    }
}
