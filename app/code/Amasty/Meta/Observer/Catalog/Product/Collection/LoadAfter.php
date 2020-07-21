<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Observer\Catalog\Product\Collection;
use Magento\Framework\Event\ObserverInterface;

class LoadAfter implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
     */
    protected $filterableAttributeList;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestInterface;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $metaHelper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layoutInterface;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $configInterface,
        \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filterableAttributeList,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\View\LayoutInterface $layoutInterface,
        \Amasty\Meta\Helper\Data $metaHelper
    ) {
        $this->_coreRegistry = $registry;
        $this->requestInterface = $requestInterface;
        $this->_scopeConfig = $configInterface;
        $this->catalogHelper = $catalogHelper;
        $this->_storeManager = $storeManagerInterface;
        $this->metaHelper = $metaHelper;
        $this->filterableAttributeList = $filterableAttributeList;
        $this->layoutInterface = $layoutInterface;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_scopeConfig->getValue('ammeta/product/enabled')) {
            return;
        }

        if ($this->requestInterface->getControllerName() == 'product')
            return;

        $productCollection = $observer->getCollection();
        if (0 < $productCollection->getSize()) {
            $forceOverwrite = $this->_scopeConfig->isSetFlag('ammeta/product/force');
            foreach ($productCollection as $product) {
                if (!$forceOverwrite && trim($product->getData('short_description'))) {
                    continue;
                }

                $block = $this->layoutInterface->createBlock('Magento\Cms\Block\Block');

                $block->setProduct($product);

                $catPaths = [];

                if ($product->getCategory()) {
                    $categories = array($product->getCategory());
                } else {
                    $categories = $product->getCategoryCollection();
                }

                foreach ($categories as $category)
                    $catPaths = array_reverse($category->getPathIds());

                if (!empty($catPaths)) {
                    $path = '/catalog/product/view/id/' . $product->getId() . '/category/' . $catPaths[0];
                    $block->setPath($path);
                }
            }
        }
    }
}
