<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RebuildUrl\Model\Product;

use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Category;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\Catalog\Model\Product;

class Url extends \Magento\Catalog\Model\Product\Url
{
    /** @var array */
    protected $products = [];

    /**
     * @var CategoryProcessor
     */
    protected $categoryProcessor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /** @var UrlFinderInterface */
    protected $urlFinder;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var UrlRewriteFactory */
    protected $urlRewriteFactory;

    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var array */
    protected $storesCache = [];

    /**
     * @var \Zemi\RebuildUrl\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * Url constructor.
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param CategoryProcessor $categoryProcessor
     * @param \Psr\Log\LoggerInterface $logger
     * @param UrlPersistInterface $urlPersist
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param UrlFinderInterface $urlFinder
     * @param \Zemi\RebuildUrl\Helper\Data $helperData
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator,
        \Zemi\RebuildUrl\Model\Product\CategoryProcessor $categoryProcessor,
        \Psr\Log\LoggerInterface $logger,
        UrlPersistInterface $urlPersist,
        UrlRewriteFactory $urlRewriteFactory,
        UrlFinderInterface $urlFinder,
        \Zemi\RebuildUrl\Helper\Data $helperData,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        parent::__construct(
            $urlFactory, $storeManager, $filter, $sidResolver, $urlFinder, $data
        );
        $this->categoryProcessor = $categoryProcessor;
        $this->loger = $logger;
        $this->urlPersist = $urlPersist;
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlFinder = $urlFinder;
        $this->helperData = $helperData;
        $this->_eavConfig = $eavConfig;
    }

    /**
     * Refresh all product rewrites for designated store
     * @param $storeId
     * @return Url
     */
    public function startGenerateUrl($storeId)
    {
        $lastEntityId = 0;
        $process = true;
        while ($process == true) {
            $this->products = $this->_getResource()->getProductsByStore(
                $storeId, $lastEntityId
            );
            if (!$this->products) {
                $process = false;
                break;
            }
            $productUrls = $this->generateUrls($storeId);
            if ($productUrls) {
                try {
                    $this->urlPersist->replace($productUrls);
                } catch (\Exception $e) {
                    $logString = "[URL_BUILD_ERROR] " . $e->getMessage();
                    $this->logger->error($logString);
                }
            }
        }
        return $this;
    }

    /**
     * Get url resource instance
     */
    protected function _getResource()
    {
        return ObjectManager::getInstance()->get(
            \Zemi\RebuildUrl\Model\ResourceModel\Product\Url::class
        );
    }


    /**
     * @param null $storeId
     * @return \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Api\Data\StoreInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStores($storeId = null)
    {
        if ($storeId) {
            return $this->storeManager->getStore($storeId);
        }
        return $this->storeManager->getStores($storeId);
    }

    /**
     * @param $storeId
     * @return array
     */
    protected function generateUrls($storeId)
    {
        /**
         * @var $urls \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
         */
        $urls = array_merge(
            $this->canonicalUrlRewriteGenerate($storeId), //Regular product URLs with target// /catalog/product/view/id/{ID}
            $this->categoriesUrlRewriteGenerate($storeId), //Regular product URLs but with target// /catalog/product/view/id/{ID}/category/{CAT}
            $this->currentUrlRewritesRegenerate($storeId) //Used to not update existing rewrites, if they exist
        );

        /* Reduce duplicates. Last wins */
        $result = [];
        $this->helperData->startProgressBar('Started generating URL rewrites.', count($urls));
        foreach ($urls as $url) {
            $result[$url->getTargetPath() . '-' . $url->getStoreId()] = $url;
            $this->helperData->advanceProgressBar();
        }
        $this->products = [];
        $this->helperData->finishProgressBar('Regenerated URL rewrites successfully');
        return $result;

    }

    /**
     * Generate list based on store view
     *
     * @return UrlRewrite[]
     */
    protected function canonicalUrlRewriteGenerate($storeId)
    {
        $urls = [];
        foreach ($this->products as $product) {
            if ($this->productUrlPathGenerator->getUrlPath($product)) {
                $urlTargetPath = $this->productUrlPathGenerator->getCanonicalUrlPath($product); //Get the unrewritten url (i.e. /catalog/product/view/id/) as the target
                $urlRequestPath = $this->productUrlPathGenerator->getUrlPathWithSuffix($product, $storeId);
                $urls[] = $this->urlRewriteFactory->create()
                    ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                    ->setEntityId($product->getId())
                    ->setRequestPath($urlRequestPath)
                    ->setTargetPath($urlTargetPath)
                    ->setStoreId($storeId);
            }
        }
        return $urls;
    }

    /**
     * Generate list based on categories
     *
     * @param $storeId
     * @return UrlRewrite[]
     */
    protected function categoriesUrlRewriteGenerate($storeId)
    {
        $urls = [];
        foreach ($this->products as $product) {
            foreach ($product->getCategoryIds() as $categoryId) {
                $category = $this->categoryProcessor->getCategoryById($categoryId);
                $requestPath = $this->productUrlPathGenerator->getUrlPathWithSuffix(
                    $product, $storeId, $category
                );
                $urls[] = $this->urlRewriteFactory->create()
                    ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                    ->setEntityId($product->getId())
                    ->setRequestPath($requestPath)
                    ->setTargetPath(
                        $this->productUrlPathGenerator->getCanonicalUrlPath(
                            $product, $category
                        )
                    )
                    ->setStoreId($storeId)
                    ->setMetadata(['category_id' => $category->getId()]);
            }
        }
        return $urls;
    }

    /**
     * Generate list based on current rewrites
     *
     * @param $storeId
     * @return UrlRewrite[]
     */
    protected function currentUrlRewritesRegenerate($storeId)
    {
        $currentUrlRewrites = $this->urlFinder->findAllByData(
            [
                UrlRewrite::STORE_ID => array_keys($this->storesCache),
                UrlRewrite::ENTITY_ID => array_keys($this->products),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
            ]
        );
        $urlRewrites = [];
        foreach ($currentUrlRewrites as $currentUrlRewrite) {
            $category = $this->retrieveCategoryFromMetadata($currentUrlRewrite);
            if ($category === false) {
                continue;
            }
            $url = $currentUrlRewrite->getIsAutogenerated()
                ? $this->generateForAutogenerated($currentUrlRewrite, $category)
                : $this->generateForCustom($currentUrlRewrite, $category);
            $urlRewrites = array_merge($urlRewrites, $url);
        }
        return $urlRewrites;
    }

    /**
     * @param UrlRewrite $url
     * @return Category|null|bool
     */
    protected function retrieveCategoryFromMetadata($url)
    {
        $metadata = $url->getMetadata();
        if (isset($metadata['category_id'])) {
            $category = $this->categoryProcessor->getCategoryById($metadata['category_id']);
            return $category === null ? false : $category;
        }
        return null;
    }

    /**
     * @param UrlRewrite $url
     * @param Category $category
     * @return array
     */
    protected function generateForAutogenerated($url, $category)
    {
        $storeId = $url->getStoreId();
        $productId = $url->getEntityId();
        if (isset($this->products[$productId][$storeId])) {
            $product = $this->products[$productId][$storeId];
            if (!$product->getData('save_rewrites_history')) {
                return [];
            }
            $targetPath = $this->productUrlPathGenerator->getUrlPathWithSuffix($product, $storeId, $category);
            if ($url->getRequestPath() === $targetPath) {
                return [];
            }
            return [
                $this->urlRewriteFactory->create()
                    ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                    ->setEntityId($productId)
                    ->setRequestPath($url->getRequestPath())
                    ->setTargetPath($targetPath)
                    ->setRedirectType(OptionProvider::PERMANENT)
                    ->setStoreId($storeId)
                    ->setDescription($url->getDescription())
                    ->setIsAutogenerated(0)
                    ->setMetadata($url->getMetadata())
            ];
        }
        return [];
    }

    /**
     * @param UrlRewrite $url
     * @param Category $category
     * @return array
     */
    protected function generateForCustom($url, $category)
    {
        $storeId = $url->getStoreId();
        $productId = $url->getEntityId();
        if (isset($this->products[$productId][$storeId])) {
            $product = $this->products[$productId][$storeId];
            $targetPath = $url->getRedirectType()
                ? $this->productUrlPathGenerator->getUrlPathWithSuffix($product, $storeId, $category)
                : $url->getTargetPath();
            if ($url->getRequestPath() === $targetPath) {
                return [];
            }
            return [
                $this->urlRewriteFactory->create()
                    ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                    ->setEntityId($productId)
                    ->setRequestPath($url->getRequestPath())
                    ->setTargetPath($targetPath)
                    ->setRedirectType($url->getRedirectType())
                    ->setStoreId($storeId)
                    ->setDescription($url->getDescription())
                    ->setIsAutogenerated(0)
                    ->setMetadata($url->getMetadata())
            ];
        }
        return [];
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cleanupCatalogUrlRewrites()
    {
        $connection = $this->helperData->getConnection();

        $cleanupUrlRewritesSelect = $connection->select()
            ->from($this->helperData->_getTableName('url_rewrite'))
            ->where('entity_type = ?', 'category')
            ->orWhere('entity_type = ?', 'product');
        $cleanupUrlRewritesQuery = $cleanupUrlRewritesSelect->deleteFromSelect($this->helperData->_getTableName('url_rewrite'));
        $connection->query($cleanupUrlRewritesQuery);

        $categoryUrlKeyAttribute = $this->_eavConfig->getAttribute(
            Category::ENTITY,
            'url_key'
        );
        $categoryUrlPathAttribute = $this->_eavConfig->getAttribute(
            Category::ENTITY,
            'url_path'
        );
        if (!empty($categoryUrlKeyAttribute) && !empty($categoryUrlPathAttribute)) {
            $cleanupCategoryAttributesSelect = $connection->select()
                ->from($this->helperData->_getTableName('catalog_category_entity_varchar'))
                ->where('attribute_id = ?', $categoryUrlKeyAttribute->getId())
                ->orWhere('attribute_id = ?', $categoryUrlPathAttribute->getId());
            $cleanupCategoryAttributesQuery = $cleanupCategoryAttributesSelect->deleteFromSelect($this->helperData->_getTableName('catalog_category_entity_varchar'));
            $connection->query($cleanupCategoryAttributesQuery);
        }

        $productUrlKeyAttribute = $this->_eavConfig->getAttribute(
            Product::ENTITY,
            'url_key'
        );
        $productUrlPathAttribute = $this->_eavConfig->getAttribute(
            Product::ENTITY,
            'url_path'
        );
        if (!empty($productUrlKeyAttribute) && !empty($productUrlPathAttribute)) {
            $cleanupCategoryAttributesSelect = $connection->select()
                ->from($this->helperData->_getTableName('catalog_product_entity_varchar'))
                ->where('attribute_id = ?', $productUrlKeyAttribute->getId())
                ->orWhere('attribute_id = ?', $productUrlPathAttribute->getId());
            $cleanupCategoryAttributesQuery = $cleanupCategoryAttributesSelect->deleteFromSelect($this->helperData->_getTableName('catalog_product_entity_varchar'));
            $connection->query($cleanupCategoryAttributesQuery);
        }

        $connection->query(
            "UPDATE " . $connection->getTableName('catalog_product_entity_varchar') . "
                SET value = ''
            WHERE attribute_id = " . $this->_getProductAttributeId('url_key')
        );

        return $this;
    }
}