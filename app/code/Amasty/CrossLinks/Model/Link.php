<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use \Magento\Framework\Model\AbstractModel;
use \Amasty\CrossLinks\Model\Source\ReferenceType;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Link
 * @package Amasty\CrossLinks\Model
 */
class Link extends AbstractModel implements \Amasty\CrossLinks\Api\LinkInterface
{
    const DEFAULT_LINK_URL = '#';
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var \Amasty\CrossLinks\Helper\Data
     */
    protected $_helper;

    /**
     * @var array
     */
    protected $_entityOrderData = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * AbstractGiftCardEntity constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Amasty\CrossLinks\Helper\Data $helper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\CrossLinks\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_helper = $helper;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\CrossLinks\Model\ResourceModel\Link');
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setData('title', $title);
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * @return array
     */
    public function getKeywords()
    {
        return explode("\r\n", $this->getData('keywords'));
    }

    /**
     * @param $innerText
     * @return string
     */
    public function getLinkHtml($innerText)
    {
        return '<a href="' . $this->getLinkUrl(). '" title="' . $this->getTitle() . '"'
            . ($this->getIsNofollow() ? ' rel="nofollow"' : '')
            . ' target="' . $this->getLinkTarget() . '"'
            . '>' . $innerText . '</a>';
    }

    /**
     * @return string
     */
    public function getLinkUrl()
    {
        switch ($this->getReferenceType()) {
            case ReferenceType::REFERENCE_TYPE_CUSTOM:
                $uri = $this->getCustomUrl();
                break;
            case ReferenceType::REFERENCE_TYPE_PRODUCT:
                $uri = $this->getProductUrl();
                break;
            case ReferenceType::REFERENCE_TYPE_CATEGORY:
                $uri = $this->getCategoryUrl();
                break;
            default:
                $uri = self::DEFAULT_LINK_URL;
        }

        return $uri;
    }

    /**
     * @return string
     */
    protected function getCustomUrl()
    {
        return strpos($this->getReferenceResource(), 'http') === 0 ?
            $this->getReferenceResource()
            : $this->storeManager->getStore()->getBaseUrl() . trim($this->getReferenceResource(), '/');
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    public function getProduct()
    {
        if (!$this->getData('product')) {
            try {
                $product = $this->productRepository->getById($this->getReferenceResource());
                $this->setData('product', $product);
            } catch (NoSuchEntityException $e) {
                //skip
            }
        }
        return $this->getData('product');
    }

    /**
     * @return \Magento\Catalog\Api\Data\CategoryInterface|null
     */
    public function getCategory()
    {
        if (!$this->getData('category')) {
            try {
                $category = $this->categoryRepository->get($this->getReferenceResource());
                $this->setData('category', $category);
            } catch (NoSuchEntityException $e) {
                //skip
            }
        }
        return $this->getData('category');

    }

    /**
     * @return string|null
     */
    protected function getProductUrl()
    {
        if (!$this->getData('product_url')) {
            if ($product = $this->getProduct()) {
                $this->setData('product_url', $product->getProductUrl());
            } else {
                $this->setData('product_url', self::DEFAULT_LINK_URL);
            }
        }
        return $this->getData('product_url');
    }

    /**
     * @return string|null
     */
    protected function getCategoryUrl()
    {
        if (!$this->getData('category_url')) {
            if ($category = $this->getCategory()) {
                $this->setData('category_url', $category->getUrl());
            } else {
                $this->setData('category_url', self::DEFAULT_LINK_URL);
            }
        }
        return $this->getData('category_url');
    }
}
