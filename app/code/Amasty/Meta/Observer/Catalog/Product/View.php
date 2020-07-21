<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Observer\Catalog\Product;
use Magento\Framework\Event\ObserverInterface;

class View implements ObserverInterface
{

    /**
     * @var \Amasty\Meta\Helper\UrlKeyHandler
     */
    protected $_helperUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    public function __construct(
        \Amasty\Meta\Helper\UrlKeyHandler $helperUrl,
        \Amasty\Meta\Helper\Data $data,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_helperUrl = $helperUrl;
        $this->data = $data;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();

        $this->data->observeProductPage($product);

        if ($product->getNeedUpdateProductUrl()) {
            $store = $this->_storeManager->getStore($product->getStoreId());
            $this->_helperUrl->processProduct($product, $store);
        }
    }
}
