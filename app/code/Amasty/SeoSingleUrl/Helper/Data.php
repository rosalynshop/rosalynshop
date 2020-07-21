<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Helper;

use Amasty\SeoSingleUrl\Model\Source\By;
use Amasty\SeoSingleUrl\Model\Source\Type;
use Magento\Store\Model\ScopeInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MODULE_PATH = 'amasty_seourl/';

    protected $categoryData = null;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Amasty\SeoSingleUrl\Model\UrlRewrite\Storage
     */
    private $urlFinder;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Amasty\SeoSingleUrl\Model\UrlRewrite\Storage $urlFinder
    ) {
        parent::__construct($context);
        $this->jsonEncoder = $jsonEncoder;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->urlFinder = $urlFinder;
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function getModuleConfig($path)
    {
        return $this->scopeConfig->getValue(self::MODULE_PATH . $path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $product
     * @param $storeId
     *
     * @return string
     */
    public function getSeoUrl($product, $storeId)
    {
        $requestPath = $this->generateSeoUrl($product->getId(), $storeId);
        if ($requestPath) {
            $product->setRequestPath($requestPath);
        }

        return $requestPath;
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return string
     */
    public function generateSeoUrl($productId, $storeId)
    {
        $filterData = [
            UrlRewrite::ENTITY_ID => $productId,
            UrlRewrite::ENTITY_TYPE => \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::STORE_ID => $storeId,
        ];

        if ($this->getModuleConfig('general/product_url_type') == Type::NO_CATEGORIES) {
            $rewrite = $this->urlFinder->getUrlWithoutCategory($filterData);

            return $rewrite ? $rewrite->getRequestPath() : '';
        }
        $rewrites = $this->urlFinder->findAllByDataWithoutCategory($filterData);

        $ulrVariants = [];
        $simplePath = '';
        foreach ($rewrites as $rewrite) {
            if ($rewrite->getRedirectType() != '0') {
                continue;//remove old pages with 301 302 redirect
            }

            $path = $rewrite->getRequestPath();
            if (!$simplePath) {
                $simplePath = $path;
            }

            $path = ltrim($path, '/');
            $path = $this->replaceExcludedCategories($path, $storeId);
            if (strpos($path, '/') === false) {
                continue;
            }

            $ulrVariants[] = $path;
        }

        $requestPath = '';
        if ($ulrVariants) {
            $requestPath = $this->getVariantBySetting($ulrVariants);
        }

        if (!$requestPath) {
            $requestPath = $simplePath;
        }

        return $requestPath;
    }

    private function getCategoryData($storeId)
    {
        if ($this->categoryData === null) {
            $this->categoryData = [];
            $collection = $this->categoryCollectionFactory->create()
                ->addAttributeToSelect('url_key')
                ->addFieldToFilter('entity_id', ['in' => $this->getExcludedCategoryIds()])
                ->setStoreId($storeId);
            foreach ($collection as $category) {
                if ($category->getUrlKey()) {
                    $this->categoryData[] = $category->getUrlKey();
                }
            }
        }

        return $this->categoryData;
    }

    private function replaceExcludedCategories($path, $storeId)
    {
        $categoryUrls = $this->getCategoryData($storeId);
        if ($categoryUrls) {
            $pathArray = explode('/', $path);
            foreach ($categoryUrls as $categoryUrl) {
                $key = array_search($categoryUrl, $pathArray);
                if ($key !== false) {
                    $path = '';
                    break;
                }
            }
        }

        return $path;
    }

    private function getExcludedCategoryIds()
    {
        $ids = $this->getModuleConfig('general/exclude');
        $ids = str_replace(' ', '', $ids);
        $ids = explode(',', $ids);

        return $ids;
    }

    private function getVariantBySetting($urlVariants)
    {
        $type = $this->getModuleConfig('general/product_url_type');
        $by = $this->getModuleConfig('general/by');
        if ($by == By::CHARACTER_NUMBER) {
            usort($urlVariants, function ($first, $second) {
                $positionFirst = strlen($first);
                $positionSecond = strlen($second);
                return $positionFirst - $positionSecond;
            });
            if ($type == Type::LONGEST) {
                $urlVariants = array_reverse($urlVariants);
            }
        } else {
            if ($type == Type::SHORTEST) {
                $firstResult = -1;
                $secondResult = 1;
            } else {
                $firstResult = 1;
                $secondResult = -1;
            }
            usort($urlVariants, function ($first, $second) use ($firstResult, $secondResult) {
                $firstLength = count(explode('/', $first));
                $secondLength = count(explode('/', $second));
                return ($firstLength < $secondLength) ? $firstResult : $secondResult;
            });
        }

        return $urlVariants[0];
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isUseCategoriesPath()
    {
        return $this->scopeConfig->getValue(
            'general/product_use_categories',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getProductUrlType()
    {
        return $this->scopeConfig->getValue(
            'general/product_url_type',
            ScopeInterface::SCOPE_STORE
        );
    }
}
