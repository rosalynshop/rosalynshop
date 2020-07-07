<?php
/**
 * @author   Rosalynshop <info@rosalynshop.com>
 * @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\SalesEmail\Model\Order;

use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Item
 * @package RosalynShop\SalesEmail\Model\Order
 */
class Item extends \Magento\Sales\Model\Order\Item
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * Item constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->_imageHelper = $imageHelper;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $orderFactory,
            $storeManager,
            $productRepository,
            $resource,
            $resourceCollection,
            $data,
            $serializer
        );
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
            $imageUrl = $this->_imageHelper->init($product, 'product_base_image')
                ->setImageFile($product->getImage()) // image,small_image,thumbnail
                ->resize(250)
                ->getUrl();
        } else {
            $imageUrl = $this->_imageHelper->init($this->getProduct(), 'product_base_image')
                ->setImageFile($this->getProduct()->getImage()) // image,small_image,thumbnail
                ->resize(250)
                ->getUrl();
        }
        return $imageUrl;
    }
}
