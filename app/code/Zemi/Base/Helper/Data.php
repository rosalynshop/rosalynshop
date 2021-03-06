<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Zemi\Base\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->_imageHelper = $imageHelper;
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $sku
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductImage($sku)
    {
        $imageUrl = null;
        if (!empty($sku)) {
            $product = $this->productRepository->get($sku);
            $imageUrl = $this->_imageHelper->init($product, 'product_base_image')->getUrl();
        } else {
            $imageUrl = $this->_imageHelper->init($this->getProduct(), 'product_base_image')->getUrl();
        }
        return $imageUrl;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
