<?php

namespace RosalynShop\Productslider\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Product
 * @package RosalynShop\Productslider\Block\Widget
 */
class Product extends Template implements BlockInterface
{
    protected $_template = "widget/product.phtml";

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /*
     *
     */
    protected $_priceHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    protected $bannerRepository;

    /**
     * Product constructor.
     * @param Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Pricing\Helper\Data $_priceHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $_priceHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Codilar\BannerSlider\Model\BannerRepository $bannerRepository,
        array $data = [])
    {
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_priceHelper = $_priceHelper;
        $this->imageHelper = $imageHelper;
        $this->bannerRepository = $bannerRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return array|false|string[]
     */
    public function getCategoryIds()
    {
        $categoryArray = [];
        if($this->hasData('category_ids')){
            $categoryIds = $this->getData('category_ids');
            $categoryArray = explode(',',$categoryIds);
        }
        return $categoryArray;
    }

    /**
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollecttion()
    {
        $categoryArray = $this->getCategoryIds();
        $products = [];
        if(count($categoryArray)>0){
            foreach($categoryArray as $categoryId){
                $category = $this->_categoryFactory->create()->load($categoryId);
                $collection = $this->_productCollectionFactory->create();
                $collection->addAttributeToSelect('*');
                $collection->addCategoryFilter($category);
                $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                if($category->getId()){
                    $products = $collection;
                }
            }
        }
        return $products;
    }

    /**
     * @param $product
     * @return float|string
     */
    public function getProductPrice($product)
    {
        return $this->_priceHelper->currency($product->getFinalPrice(), true, false);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }

    /**
     * @param $product
     * @param $imageId
     * @return string
     */
    public function getProductImage($product, $imageId)
    {
        return $this->imageHelper->init($product, $imageId)
            ->setImageFile($product->getSmallImage()) // image,small_image,thumbnail
            ->resize(380)
            ->getUrl();
    }

    /**
     * @return array
     */
    public function getBannerSlider()
    {
        $banner = $this->bannerRepository->getCollection()->getData();
        if (!empty($banner)) {
            return $banner;
        }
        return [];
    }
}