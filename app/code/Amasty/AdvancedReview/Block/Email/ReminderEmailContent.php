<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Email;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class ReminderEmailContent
 * @package Amasty\AdvancedReview\Block
 */
class ReminderEmailContent extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::email/products.phtml';

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var array
     */
    private $productIds;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * @var \Magento\Framework\Url
     */
    private $url;

    public function __construct(
        Template\Context $context,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UrlFinderInterface $urlFinder,
        \Magento\Framework\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->urlFinder = $urlFinder;
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        $ids = $this->getProductIds();
        $ids = array_unique($ids);

        $this->searchCriteriaBuilder->addFilter('entity_id', $ids, 'in');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();

        return $products;
    }

    /**
     * Create function for getting only front url in email
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getProductUrl(\Magento\Catalog\Model\Product $product)
    {
        $params = [];
        $params['_nosid'] = true;
        $routePath = '';
        $routeParams = $params;

        $storeId = $product->getStoreId();

        $categoryId = null;

        if ($product->hasUrlDataObject()) {
            $requestPath = $product->getUrlDataObject()->getUrlRewrite();
            $routeParams['_scope'] = $product->getUrlDataObject()->getStoreId();
        } else {
            $requestPath = $product->getRequestPath();
            if (empty($requestPath) && $requestPath !== false) {
                $filterData = [
                    UrlRewrite::ENTITY_ID => $product->getId(),
                    UrlRewrite::ENTITY_TYPE => \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator::ENTITY_TYPE,
                    UrlRewrite::STORE_ID => $storeId,
                ];
                if ($categoryId) {
                    $filterData[UrlRewrite::METADATA]['category_id'] = $categoryId;
                }
                $rewrite = $this->urlFinder->findOneByData($filterData);
                if ($rewrite) {
                    $requestPath = $rewrite->getRequestPath();
                    $product->setRequestPath($requestPath);
                } else {
                    $product->setRequestPath(false);
                }
            }
        }

        if (!empty($requestPath)) {
            $routeParams['_direct'] = $requestPath;
        } else {
            $routePath = 'catalog/product/view';
            $routeParams['id'] = $product->getId();
            $routeParams['s'] = $product->getUrlKey();
            if ($categoryId) {
                $routeParams['category'] = $categoryId;
            }
        }

        // reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = [];
        }

        return $this->url->setScope($storeId)->getUrl($routePath, $routeParams);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * @return array
     */
    public function getProductIds()
    {
        return $this->productIds;
    }

    /**
     * @param $productIds
     *
     * @return $this
     */
    public function setProductIds($productIds)
    {
        $this->productIds = $productIds;
        return $this;
    }
}
