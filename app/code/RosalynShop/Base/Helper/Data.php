<?php
/**
 * @author   Rosalynshop <info@rosalynshop.com>
 * @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package RosalynShop\Base\Helper
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
     * Data constructor.
     * @param Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->productRepository = $productRepository;
        $this->_imageHelper = $imageHelper;
        $this->customerSession = $customerSession;
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
}
