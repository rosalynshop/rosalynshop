<?php

namespace RosalynShop\Productslider\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Product extends Template implements BlockInterface
{
    protected $_template = "widget/product.phtml";
    protected $_categoryFactory;
    protected $_productCollectionFactory;
    protected $_priceHelper;

    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $_priceHelper,
        array $data = [])
    {
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_priceHelper = $_priceHelper;
        parent::__construct($context, $data);
    }

    public function getCategoryIds()
    {
        $categoryArray = [];
        if($this->hasData('category_ids')){
            $categoryIds = $this->getData('category_ids');
            $categoryArray = explode(',',$categoryIds);
        }
        return $categoryArray;
    }


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
}