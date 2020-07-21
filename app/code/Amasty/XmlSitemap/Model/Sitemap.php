<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model;

use Amasty\XmlSitemap\Model\Hreflang\XmlTagsProviderFactory;
use Amasty\XmlSitemap\Model\Hreflang\XmlTagsProviderInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface as XmlUrlInterface;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Amasty\XmlSitemap\Api\SitemapInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter;

/**
 * Class Sitemap
 */
class Sitemap extends AbstractModel implements SitemapInterface
{
    const SITEMAP_GENERATION = 'amasty_xml_sitemap';
    const PAGE_SIZE = 500;

    const XML_LINE = '<url><loc>%s</loc><changefreq>%s</changefreq><priority>%.2f</priority></url>';
    const XML_LINE_OPEN = '<url><loc>%s</loc><priority>%.2f</priority><changefreq>%s</changefreq>';
    const XML_LINE_CLOSE = '</url>';
    const XML_LINE_IMAGE_OPEN = '<image:image>';
    const XML_LINE_IMAGE_CLOSE = '<image:loc>%s</image:loc></image:image>';
    const XML_LINE_IMAGE_TITLE = '<image:title>%s</image:title>';
    const XML_LINE_IMAGE_CAPTION = '<image:caption>%s</image:caption>';
    const XML_LINE_LASTMOD = '<lastmod>%s</lastmod>';

    private $date;
    private $xml = [];
    private $baseUrl;
    private $storeId;
    private $iterator = 1;
    private $excludeUrls;
    private $isFirst = true;
    private $firstTime;
    private $filePath;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_productCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $_categoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $_pageCollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList $directoryList
     */
    private $directoryList;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write $directory
     */
    private $directory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility $productVisibility
     */
    private $productVisibility;

    /**
     * @var \Magento\Framework\Module\Manager $moduleManager
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface $messageManager
     */
    private $messageManager;

    /**
     * @var \Amasty\XmlSitemap\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var GalleryReadHandler
     */
    private $galleryReadHandler;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    private $stockHelper;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $appEmulation;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var XmlTagsProviderInterface
     */
    private $hreflangTagsProvider;

    /**
     * @var XmlTagsProviderFactory
     */
    private $hreflangTagsProviderFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Amasty\XmlSitemap\Helper\Data $helper,
        \Magento\Catalog\Helper\Image $imageHelper,
        GalleryReadHandler $galleryReadHandler,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        XmlTagsProviderFactory $hreflangTagsProviderFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->ioFile = $ioFile;
        $this->directoryList = $dir;
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->productVisibility = $productVisibility;
        $this->moduleManager = $moduleManager;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        $this->imageHelper = $imageHelper;
        $this->galleryReadHandler = $galleryReadHandler;
        $this->stockHelper = $stockHelper;
        $this->appEmulation = $appEmulation;
        $this->categoryRepository = $categoryRepository;
        $this->hreflangTagsProviderFactory = $hreflangTagsProviderFactory;
        $this->escaper = $escaper;
    }

    protected function _construct()
    {
        $this->_init(\Amasty\XmlSitemap\Model\ResourceModel\Sitemap::class);
    }

    public function run()
    {
        $this->generateXml();
        $this->_removeOldFiles();
    }

    /**
     * @return XmlTagsProviderInterface
     */
    private function getHreflangTagsProvider()
    {
        if (!$this->hreflangTagsProvider) {
            $this->hreflangTagsProvider = $this->hreflangTagsProviderFactory->get($this->getStoreId());
        }

        return $this->hreflangTagsProvider;
    }

    public function generateXml()
    {
        $this->_registry->register(self::SITEMAP_GENERATION, true);
        $this->storeId = $this->getStoreId();
        $this->appEmulation->startEnvironmentEmulation($this->storeId);
        $currentStore = $this->storeManager->getStore()->getId();
        $this->storeManager->setCurrentStore($this->storeId);

        $this->baseUrl = $this->storeManager->getStore($this->storeId)
            ->getBaseUrl(XmlUrlInterface::URL_TYPE_LINK);
        
        $this->generateProducts();
        $this->generateCategories();
        $this->generateCms();
        $this->generateExtra();

        $this->generateLanding();
        $this->generateBlog();

        $this->generateBrands();
        $this->generateNavigation();

        $this->generateFaq();

        $this->appEmulation->stopEnvironmentEmulation();
        $this->storeManager->setCurrentStore($currentStore);

        $pieces = [];
        $this->iterator = 0;
        $isChunk = $this->getMaxItems() > 0 && count($this->xml) > $this->getMaxItems();

        if ($isChunk) {
            $split = array_chunk($this->xml, $this->getMaxItems(), false);
            foreach ($split as $chunk) {
                // phpcs:ignore
                $pieces = array_merge($pieces, $this->_writePortion($chunk, false));
            }
        } else {
            $pieces = $this->_writePortion($this->xml, true);
            if (count($pieces) > 1) {
                $this->_renameFirstFile($pieces);
            }
        }

        if (count($pieces) > 1) {
            $this->_writeIndexFile($pieces);
        }

        $this->setLastRun($this->dateTime->gmtDate());
        $this->save();

        $this->_registry->unregister(self::SITEMAP_GENERATION);

        return $this;
    }

    public function parsePlaceholder($product)
    {
        $txt = $this->getProductsCaptionsTemplate();

        if ($txt == '') {
            return $txt;
        }

        $vars = [];
        preg_match_all('/{([a-zA-Z:\_0-9]+)}/', $txt, $vars);

        if (!$vars[1]) {
            return $txt;
        }
        $vars = $vars[1];

        foreach ($vars as $var) {
            $value = '';
            switch ($var) {
                case 'product_name':
                    $value = $product->getName();
                    break;
            }
            $txt = str_replace('{' . $var . '}', $value, $txt);
        }

        return $txt;
    }

    public function beforeSave()
    {
        if (!preg_match('#\.xml$#', $this->getFolderName())) {
            if (substr($this->getFolderName(), strlen($this->getFolderName()) - 1) == "/") {
                $this->setFolderName(substr($this->getFolderName(), 0, strlen($this->getFolderName()) - 1));
            }
            $this->setFolderName($this->getFolderName() . '.xml');
        }

        $realPath = $this->ioFile->getCleanPath($this->getPath());

        if (!$this->ioFile->allowedPath($realPath, $this->directoryList->getRoot())) {
            $this->messageManager->addErrorMessage(__('Please define correct path'));
        } elseif (!$this->ioFile->fileExists($realPath, false)) {
            $this->messageManager->addErrorMessage(
                __(
                    'Please create the specified folder %1 before saving the sitemap.',
                    $this->getPreparedFilename()
                )
            );
        } elseif (!$this->ioFile->isWriteable($realPath)) {
            $this->messageManager->addErrorMessage(
                __('Please make sure that %1 is writable by web-server.', $this->getPreparedFilename())
            );
        }

        return parent::beforeSave();
    }

    /**
     * Return full file name with path
     *
     * @return string
     */
    public function getPreparedFilename()
    {
        return $this->getPath() . $this->getSitemapFilename();
    }

    /**
     * Return real file path
     *
     * @return string
     */
    private function getPath()
    {
        if ($this->filePath === null) {
            // phpcs:ignore
            $dirname = pathinfo($this->getFolderName());

            if ($dirname['dirname'] == '.') {
                $this->filePath = str_replace('//', '/', $this->directoryList->getRoot() . '/');
            } else {
                $this->filePath = str_replace(
                    '//',
                    '/',
                    $this->directoryList->getRoot() . '/' . $dirname['dirname'] . '/'
                );
            }
        }

        return $this->filePath;
    }

    private function _removeOldFiles()
    {
        $realPath = $this->ioFile->getCleanPath($this->getPath());
        $fileName = $this->_getSitemapFilename();
        $pos = strpos($fileName, ".xml");
        $noExtensionFileName = substr($fileName, 0, $pos);
        $fullFilePath = $realPath . $noExtensionFileName . "_*";
        // phpcs:disable
        $files = glob($fullFilePath);

        foreach ($files as $file) {
            if (filemtime($file) < $this->firstTime) {
                unlink($file);
            }
        }
        // phpcs:enable
    }

    // phpcs:ignore
    private function generateProducts()
    {
        if (!$this->getProducts()) {
            return;
        }

        $changefreq = $this->getProductsFrequency();
        $priority = $this->getProductsPriority();
        $productCollection = $this->getProductCollection();
        $lastPageNumber = $productCollection->getLastPageNumber();

        for ($currentPage = 1; $currentPage <= $lastPageNumber; $currentPage++) {
            $productCollection->setCurPage($currentPage);

            foreach ($productCollection as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $productUrl = $this->_getUrl($this->getProductUrl($product));

                if ($this->_isUrlToExclude($productUrl)) {
                    continue;
                }

                $xmlLine = self::XML_LINE_OPEN;

                if ($this->getHreflangProduct()) {
                    $xmlLine .= $this->getHreflangTagsProvider()->getProductTagAsXml($product);
                }

                $xmlParams = [
                    $this->escaper->escapeHtml($productUrl),
                    $priority,
                    $changefreq
                ];

                if ($this->getProductsThumbs()) {
                    $this->galleryReadHandler->execute($product);
                    $images = $product->getMediaGalleryImages();

                    if ($images instanceof \Magento\Framework\Data\Collection) {
                        foreach ($images as $image) {
                            /** @var \Magento\Framework\DataObject $image */
                            $imageUrl = $this->imageHelper->init(
                                $product,
                                'product_page_image_medium_no_frame',
                                ['type' => ImageEntryConverter::MEDIA_TYPE_CODE]
                            )
                                ->setImageFile($image->getFile())
                                ->getUrl();
                            $xmlLine .= self::XML_LINE_IMAGE_OPEN;

                            if ($this->getProductsCaptions()) {
                                $title = $image->getLabel();

                                if ($title == '') {
                                    $title = $this->parsePlaceholder($product);
                                }

                                $title = $this->escaper->escapeHtml($title);

                                if (!empty($title)) {
                                    $xmlLine .= self::XML_LINE_IMAGE_TITLE;
                                    $xmlLine .= self::XML_LINE_IMAGE_CAPTION;
                                    $xmlParams[] = $title;
                                    $xmlParams[] = $title;
                                }
                            }

                            $xmlLine .= self::XML_LINE_IMAGE_CLOSE;
                            $xmlParams[] = $this->escaper->escapeHtml($imageUrl);
                        }
                    }
                }

                if ($this->getProductsModified()) {
                    $xmlLine .= self::XML_LINE_LASTMOD;
                    $updateTime = strtotime($product->getUpdatedAt());
                    $xmlParams[] = $this->dateTime->date($this->getDateFormat(), $updateTime);
                }

                $xmlLine .= self::XML_LINE_CLOSE;
                $this->xml[] = vsprintf($xmlLine, $xmlParams);
            }

            $productCollection->clear();
        }
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->productCollectionFactory->create()
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->setVisibility($this->productVisibility->getVisibleInSiteIds())
            ->setStoreId($this->getStoreId())
            ->addUrlRewrite()
            ->addAttributeToSelect('name')
            ->setPageSize(self::PAGE_SIZE);

        if ($this->getExcludeOutOfStock()) {
            $this->stockHelper->addInStockFilterToCollection($productCollection);
        }

        return $productCollection;
    }

    /**
     * @param $product
     *
     * @return string
     */
    public function getProductUrl($product)
    {
        // $product->getProductUrl() return url without store code
        $productUrl = $product->getRequestPath();
        return $productUrl;
    }

    private function generateCategories()
    {
        if (!$this->getCategories()) {
            return;
        }

        $changefreq = $this->getCategoriesFrequency();
        $priority = $this->getCategoriesPriority();

        try {
            $rootCategoryId = $this->storeManager->getStore($this->storeId)->getRootCategoryId();
            $rootCategory = $this->categoryRepository->get($rootCategoryId);
        } catch (NoSuchEntityException $e) {
            $rootCategory = null;
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->categoryCollectionFactory->create()
            ->setStoreId($this->storeId)
            ->addIsActiveFilter()
            ->addUrlRewriteToResult()
            ->addAttributeToSelect('image');

        if ($rootCategory) {
            $collection->addAttributeToFilter('path', ['like' => $rootCategory->getPath() . '/%']);
        }

        $collection->load();

        /** @var \Magento\Catalog\Model\Category $item */
        foreach ($collection as $item) {
            if (empty($item['request_path'])) {
                continue;
            }

            $item['url'] = $this->_getStoreBaseUrl() . $item['request_path'];

            if ($this->_isUrlToExclude($item['url'])) {
                continue;
            }

            $xmlLine = self::XML_LINE_OPEN;

            if ($this->getHreflangCategory()) {
                $xmlLine .= $this->getHreflangTagsProvider()->getCategoryTagAsXml($item);
            }

            $xmlParams = [
                $this->escaper->escapeHtml($item['url']),
                $priority,
                $changefreq
            ];

            if ($this->getCategoriesThumbs() && $item->getImage()) {
                $xmlLine .= self::XML_LINE_IMAGE_OPEN;

                if ($this->getCategoriesCaptions()) {
                    $xmlLine .= self::XML_LINE_IMAGE_TITLE;
                    $xmlParams[] = $item->getName();
                }

                $xmlLine .= self::XML_LINE_IMAGE_CLOSE;
                $thumb = $item->getImageUrl();
                $xmlParams[] = $this->escaper->escapeHtml($thumb);
            }

            if ($this->getCategoriesModified()) {
                $xmlLine .= self::XML_LINE_LASTMOD;
                $updateTime = strtotime($item->getUpdatedAt());
                $xmlParams[] = $this->dateTime->date($this->getDateFormat(), $updateTime);
            }

            $xmlLine .= self::XML_LINE_CLOSE;
            $this->xml[] = vsprintf($xmlLine, $xmlParams);
        }

        unset($collection);
    }

    private function generateCms()
    {
        if (!$this->getPages()) {
            return;
        }

        $changefreq = $this->getPagesFrequency();
        $priority = $this->getPagesPriority();
        /** @var \Magento\Cms\Model\ResourceModel\Page\Collection $collection */
        $collection = $this->pageCollectionFactory->create();
        $collection->addStoreFilter($this->storeId);
        $collection->getSelect()->where('is_active = 1');

        /** @var \Magento\Cms\Model\Page $item */
        foreach ($collection as $item) {
            $pageUrl = $this->baseUrl . $item->getIdentifier();

            if (($this->getExcludeCmsAliases() != '' &&
                    strpos($this->getExcludeCmsAliases(), $item->getIdentifier()) !== false)
                || $this->_isUrlToExclude($pageUrl)
            ) {
                continue;
            }

            $xmlLine = self::XML_LINE_OPEN;

            if ($this->getHreflangCms()) {
                $xmlLine .= $this->getHreflangTagsProvider()->getCmsTagAsXml($item);
            }

            $xmlParams = [
                $this->escaper->escapeHtml($pageUrl),
                $priority,
                $changefreq
            ];

            if ($this->getPagesModified()) {
                $xmlLine .= self::XML_LINE_LASTMOD;
                $updateTime = strtotime($item->getUpdateTime());
                $xmlParams[] = $this->dateTime->date($this->getDateFormat(), $updateTime);
            }

            $xmlLine .= self::XML_LINE_CLOSE;
            $this->xml[] = vsprintf($xmlLine, $xmlParams);
        }

        unset($collection);
    }

    private function generateExtra()
    {
        if (!$this->getExtra()) {
            return;
        }
        
        // phpcs:ignore
        $collection = explode(chr(13), $this->getExtraLinks());
        $changefreq = $this->getExtraFrequency();
        $priority = $this->getExtraPriority();

        foreach ($collection as $item) {
            $this->xml[] = sprintf(
                self::XML_LINE,
                $this->escaper->escapeHtml(trim($item)),
                $changefreq,
                $priority
            );
        }

        unset($collection);
    }

    private function generateLanding()
    {
        if (!$this->getLanding() || !$this->moduleManager->isEnabled('Amasty_Xlanding')) {
            return;
        }

        $changefreq = $this->getLandingFrequency();
        $priority = $this->getLandingPriority();

        $landingPages = $this->getLandingPageCollection($this->storeId);

        foreach ($landingPages as $item) {
            $landingUrl = $this->baseUrl . $item->getUrl();

            if ($this->_isUrlToExclude($landingUrl)) {
                continue;
            }

            $this->xml[] = sprintf(
                self::XML_LINE,
                $this->escaper->escapeHtml($landingUrl),
                $changefreq,
                $priority
            );
        }

        unset($landingPages);
    }

    /**
     * @return array
     */
    public function getLandingPageCollection($storeId)
    {
        return [];
    }

    private function generateBlog()
    {
        if (!$this->getBlog() || !$this->moduleManager->isEnabled('Amasty_Blog')) {
            return;
        }

        $changefreq = $this->getBlogFrequency();
        $priority = $this->getBlogPriority();

        $blogLinks = $this->getBlogProLinks($this->storeId);

        foreach ($blogLinks as $link) {
            if (isset($link['url']) && $link['url']) {
                if ($this->_isUrlToExclude($link['url'])) {
                    continue;
                }

                $this->xml[] = sprintf(
                    self::XML_LINE,
                    str_replace('index.php/', '', $link['url']),
                    $changefreq,
                    $priority
                );
            }
        }
    }

    /**
     * overrided into Amasty/Blog/Plugins/XmlSitemap/Model/Sitemap.php
     * @param $storeId
     * @return array
     */
    public function getBlogProLinks($storeId)
    {
        return [];
    }

    private function generateBrands()
    {
        if (!$this->getBrands() || !$this->moduleManager->isEnabled('Amasty_ShopbyBrand')) {
            return;
        }

        $changefreq = $this->getBrandsFrequency();
        $priority = $this->getBrandsPriority();

        $brandPages = $this->getBrandCollection($this->storeId);

        foreach ($brandPages as $brand) {
            $url = $brand->getUrl();

            if ($this->_isUrlToExclude($url) || !$url) {
                continue;
            }

            $this->xml[] = sprintf(
                self::XML_LINE,
                $url,
                $changefreq,
                $priority
            );
        }

        unset($brandPages);
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getBrandCollection($storeId)
    {
        return [];
    }

    private function generateNavigation()
    {
        if (!$this->getNavigation() || !$this->moduleManager->isEnabled('Amasty_ShopbyPage')) {
            return;
        }

        $changefreq = $this->getNavigationFrequency();
        $priority = $this->getNavigationPriority();

        $collection = $this->getShopByPageCollection($this->storeId);

        foreach ($collection as $item) {
            $url = $item->getUrl();

            if ($this->_isUrlToExclude($url)) {
                continue;
            }

            $this->xml[] = sprintf(
                self::XML_LINE,
                $url,
                $changefreq,
                $priority
            );
        }

        unset($collection);
    }

    /**
     *  Well be overrided in Amasty\ShopbyPage plugin
     * @param $storeId
     * @return array|\Amasty\ShopbyPage\Model\ResourceModel\Page\Collection
     */
    public function getShopByPageCollection($storeId)
    {
        return [];
    }

    private function generateFaq()
    {
        if (!$this->getFaq() || !$this->moduleManager->isEnabled('Amasty_Faq')) {
            return;
        }

        $changefreq = $this->getFaqFrequency();
        $priority = $this->getFaqPriority();
        $baseurl = $this->_getStoreBaseUrl();

        $categoriesCollection = $this->getFaqCategoriesPageCollection($this->storeId);

        foreach ($categoriesCollection as $item) {
            $url = $baseurl . $item->getUrl();

            if ($this->_isUrlToExclude($url)) {
                continue;
            }

            $this->xml[] = sprintf(
                self::XML_LINE,
                $url,
                $changefreq,
                $priority
            );
        }

        unset($categoriesCollection);
        $questionsCollection = $this->getFaqQuestionsPageCollection($this->storeId);

        foreach ($questionsCollection as $item) {
            $url = $baseurl . $item->getUrl();

            if ($this->_isUrlToExclude($url)) {
                continue;
            }

            $this->xml[] = sprintf(
                self::XML_LINE,
                $url,
                $changefreq,
                $priority
            );
        }

        unset($questionsCollection);
    }

    /**
     * overrided into Amasty/Faq/Plugin/XmlSitemap/Model/Sitemap.php
     * @param $store
     * @return array
     */
    public function getFaqCategoriesPageCollection($store)
    {
        return [];
    }

    /**
     * overrided into Amasty/Faq/Plugin/XmlSitemap/Model/Sitemap.php
     * @param $store
     * @return array
     */
    public function getFaqQuestionsPageCollection($store)
    {
        return [];
    }

    private function _writePortion($chunk, $index = false)
    {
        $pieces = [];
        $path = $this->getPath();

        $name = $this->_getSitemapFileName();
        $this->iterator++;

        if (!$index) {
            $name = str_replace('.xml', '', $name);
            $name .= '_' . $this->iterator . '.xml';
        }

        $fullPath = $this->getDirUrl() . $name;

        /** @var \Magento\Framework\Filesystem\File\Write $stream */
        $stream = $this->directory->openFile($fullPath);
        $pieces[] = $fullPath;

        $stream->write('<?xml version="1.0" encoding="UTF-8"?>'  . PHP_EOL);
        $stream->write('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ');
        $stream->write('xmlns:xhtml="http://www.w3.org/1999/xhtml"');

        if ($this->getProductsThumbs() || $this->getCategoriesThumbs()) {
            $stream->write(' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"');
        }

        $stream->write('>' . PHP_EOL);

        $chunkCount = count($chunk);
        for ($i = 0; $i < $chunkCount; $i++) {
            if (false === strpos($chunk[$i], '<loc></loc>')) {
                $stream->write($chunk[$i] . "\n");

                if (isset($chunk[$i + 1])) {
                    $fileSize = $this->_testFileSize($fullPath, $chunk[$i + 1]);

                    if ($this->getMaxFileSize() && $fileSize > ($this->getMaxFileSize() * 1024)) {
                        $newArray = array_slice($chunk, $i + 1);
                        // phpcs:ignore
                        $pieces = array_merge($pieces, $this->_writePortion($newArray, false));
                        break;
                    }
                }
            }
        }

        $stream->write('</urlset>');
        $stream->close();

        if ($this->isFirst) {
            $this->firstTime = $this->_getAbsolutePath($fullPath);
            $this->isFirst = false;
        }

        return $pieces;
    }

    private function _renameFirstFile(&$chunks)
    {
        $path = $this->getPath();
        $this->ioFile->setAllowCreateFolders(true);
        $this->ioFile->open(['path' => $path]);

        $name = $this->_getSitemapFileName();
        $newFileName = str_replace('.xml', '', $name);
        $newFileName .= '_1.xml';

        $chunks[0] = $this->getDirUrl() . $newFileName;

        $this->ioFile->cp($name, $newFileName);
    }

    private function _getSitemapFileName()
    {
        // phpcs:ignore
        $filename = pathinfo($this->getFolderName());

        return $filename['basename'];
    }

    private function _testFileSize($testFile, $line)
    {
        $dirUrl = $this->getDirUrl();
        $fileName = 'am_sitemap_test' . rand(1, 1000) . '.xml';
        $tempFile = $dirUrl . $fileName;
        $tempFile = $this->_getAbsolutePath($tempFile);
        $testFile = $this->_getAbsolutePath($testFile);
        $this->ioFile->cp($testFile, $tempFile);
        $stream = $this->directory->openFile($tempFile, 'a');
        $stream->write($line);
        $stream->write('</urlset>');
        $stream->close();
    
        // phpcs:disable
        $fileSize = filesize($tempFile);
        unlink($tempFile);  
        // phpcs:enable
        return $fileSize;
    }

    private function _getAbsolutePath($path)
    {
        $path = $this->directoryList->getRoot() . '/' . $path;

        return $path;
    }

    private function _writeIndexFile($pieces)
    {
        $this->date = $this->dateTime->gmtDate($this->getDateFormat());
        $this->baseUrl = $this->storeManager->getStore()->getBaseUrl(XmlUrlInterface::URL_TYPE_WEB);

        $name = $this->getDirUrl() . $this->_getSitemapFileName();
        $stream = $this->directory->openFile($name);
        $stream->write('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $stream->write(
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
            . 'xmlns:xhtml="http://www.w3.org/1999/xhtml">'
        );
        foreach ($pieces as $url) {
            $item = sprintf(
                '<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>',
                $this->escaper->escapeHtml($this->helper->getCorrectUrl($url, $this->getStoreId())),
                $this->date
            );
            $stream->write($item . "\n");
        }
        $stream->write('</sitemapindex>');
        $stream->close();
    }

    private function getDirUrl()
    {
        // phpcs:ignore
        $dirname = pathinfo($this->getFolderName());

        $dirPath = $dirname['dirname'] . '/';

        if ($dirname['dirname'] == '.') {
            $dirPath = '';
        }

        return $dirPath;
    }

    private function _isUrlToExclude($url)
    {
        $isToExclude = false;

        if (empty($this->excludeUrls)) {
            $this->excludeUrls = $this->getExcludeUrls();
        }

        if ($this->excludeUrls != '' && strpos($this->excludeUrls, $url) !== false) {
            $isToExclude = true;
        }

        return $isToExclude;
    }

    /**
     * Get store base url
     *
     * @param string $type
     * @return string
     */
    protected function _getStoreBaseUrl($type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore($this->getStoreId());
        $isSecure = $store->isUrlSecure();

        return rtrim($store->getBaseUrl($type, $isSecure), '/') . '/';
    }

    /**
     * Get url
     *
     * @param string $url
     * @param string $type
     * @return string
     */
    protected function _getUrl($url, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        return $this->_getStoreBaseUrl($type) . ltrim($url, '/');
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->_getData(SitemapInterface::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->setData(SitemapInterface::TITLE, $title);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFolderName()
    {
        return $this->_getData(SitemapInterface::FOLDER_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setFolderName($folderName)
    {
        $this->setData(SitemapInterface::FOLDER_NAME, $folderName);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMaxItems()
    {
        return $this->_getData(SitemapInterface::MAX_ITEMS);
    }

    /**
     * @inheritdoc
     */
    public function setMaxItems($maxItems)
    {
        $this->setData(SitemapInterface::MAX_ITEMS, $maxItems);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMaxFileSize()
    {
        return $this->_getData(SitemapInterface::MAX_FILE_SIZE);
    }

    /**
     * @inheritdoc
     */
    public function setMaxFileSize($maxFileSize)
    {
        $this->setData(SitemapInterface::MAX_FILE_SIZE, $maxFileSize);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->_getData(SitemapInterface::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->setData(SitemapInterface::TYPE, $type);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastRun()
    {
        return $this->_getData(SitemapInterface::LAST_RUN);
    }

    /**
     * @inheritdoc
     */
    public function setLastRun($lastRun)
    {
        $this->setData(SitemapInterface::LAST_RUN, $lastRun);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(SitemapInterface::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->setData(SitemapInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCategories()
    {
        return $this->_getData(SitemapInterface::CATEGORIES);
    }

    /**
     * @inheritdoc
     */
    public function setCategories($categories)
    {
        $this->setData(SitemapInterface::CATEGORIES, $categories);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCategoriesModified()
    {
        return $this->_getData(SitemapInterface::CATEGORIES_MODIFIED);
    }

    /**
     * @inheritdoc
     */
    public function setCategoriesModified($categoriesModified)
    {
        $this->setData(SitemapInterface::CATEGORIES_MODIFIED, $categoriesModified);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCategoriesThumbs()
    {
        return $this->_getData(SitemapInterface::CATEGORIES_THUMBS);
    }

    /**
     * @inheritdoc
     */
    public function setCategoriesThumbs($categoriesThumbs)
    {
        $this->setData(SitemapInterface::CATEGORIES_THUMBS, $categoriesThumbs);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCategoriesCaptions()
    {
        return $this->_getData(SitemapInterface::CATEGORIES_CAPTIONS);
    }

    /**
     * @inheritdoc
     */
    public function setCategoriesCaptions($categoriesCaptions)
    {
        $this->setData(SitemapInterface::CATEGORIES_CAPTIONS, $categoriesCaptions);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCategoriesPriority()
    {
        return $this->_getData(SitemapInterface::CATEGORIES_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setCategoriesPriority($categoriesPriority)
    {
        $this->setData(SitemapInterface::CATEGORIES_PRIORITY, $categoriesPriority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCategoriesFrequency()
    {
        return $this->_getData(SitemapInterface::CATEGORIES_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setCategoriesFrequency($categoriesFrequency)
    {
        $this->setData(SitemapInterface::CATEGORIES_FREQUENCY, $categoriesFrequency);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPages()
    {
        return $this->_getData(SitemapInterface::PAGES);
    }

    /**
     * @inheritdoc
     */
    public function setPages($pages)
    {
        $this->setData(SitemapInterface::PAGES, $pages);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPagesPriority()
    {
        return $this->_getData(SitemapInterface::PAGES_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setPagesPriority($pagesPriority)
    {
        $this->setData(SitemapInterface::PAGES_PRIORITY, $pagesPriority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPagesFrequency()
    {
        return $this->_getData(SitemapInterface::PAGES_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setPagesFrequency($pagesFrequency)
    {
        $this->setData(SitemapInterface::PAGES_FREQUENCY, $pagesFrequency);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPagesModified()
    {
        return $this->_getData(SitemapInterface::PAGES_MODIFIED);
    }

    /**
     * @inheritdoc
     */
    public function setPagesModified($pagesModified)
    {
        $this->setData(SitemapInterface::PAGES_MODIFIED, $pagesModified);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExcludeCmsAliases()
    {
        return $this->_getData(SitemapInterface::EXCLUDE_CMS_ALIASES);
    }

    /**
     * @inheritdoc
     */
    public function setExcludeCmsAliases($excludeCmsAliases)
    {
        $this->setData(SitemapInterface::EXCLUDE_CMS_ALIASES, $excludeCmsAliases);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtra()
    {
        return $this->_getData(SitemapInterface::EXTRA);
    }

    /**
     * @inheritdoc
     */
    public function setExtra($extra)
    {
        $this->setData(SitemapInterface::EXTRA, $extra);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtraPriority()
    {
        return $this->_getData(SitemapInterface::EXTRA_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setExtraPriority($extraPriority)
    {
        $this->setData(SitemapInterface::EXTRA_PRIORITY, $extraPriority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtraFrequency()
    {
        return $this->_getData(SitemapInterface::EXTRA_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setExtraFrequency($extraFrequency)
    {
        $this->setData(SitemapInterface::EXTRA_FREQUENCY, $extraFrequency);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtraLinks()
    {
        return $this->_getData(SitemapInterface::EXTRA_LINKS);
    }

    /**
     * @inheritdoc
     */
    public function setExtraLinks($extraLinks)
    {
        $this->setData(SitemapInterface::EXTRA_LINKS, $extraLinks);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProducts()
    {
        return $this->_getData(SitemapInterface::PRODUCTS);
    }

    /**
     * @inheritdoc
     */
    public function setProducts($products)
    {
        $this->setData(SitemapInterface::PRODUCTS, $products);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductsThumbs()
    {
        return $this->_getData(SitemapInterface::PRODUCTS_THUMBS);
    }

    /**
     * @inheritdoc
     */
    public function setProductsThumbs($productsThumbs)
    {
        $this->setData(SitemapInterface::PRODUCTS_THUMBS, $productsThumbs);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductsCaptions()
    {
        return $this->_getData(SitemapInterface::PRODUCTS_CAPTIONS);
    }

    /**
     * @inheritdoc
     */
    public function setProductsCaptions($productsCaptions)
    {
        $this->setData(SitemapInterface::PRODUCTS_CAPTIONS, $productsCaptions);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductsCaptionsTemplate()
    {
        return $this->_getData(SitemapInterface::PRODUCTS_CAPTIONS_TEMPLATE);
    }

    /**
     * @inheritdoc
     */
    public function setProductsCaptionsTemplate($productsCaptionsTemplate)
    {
        $this->setData(SitemapInterface::PRODUCTS_CAPTIONS_TEMPLATE, $productsCaptionsTemplate);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductsPriority()
    {
        return $this->_getData(SitemapInterface::PRODUCTS_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setProductsPriority($productsPriority)
    {
        $this->setData(SitemapInterface::PRODUCTS_PRIORITY, $productsPriority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductsFrequency()
    {
        return $this->_getData(SitemapInterface::PRODUCTS_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setProductsFrequency($productsFrequency)
    {
        $this->setData(SitemapInterface::PRODUCTS_FREQUENCY, $productsFrequency);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductsModified()
    {
        return $this->_getData(SitemapInterface::PRODUCTS_MODIFIED);
    }

    /**
     * @inheritdoc
     */
    public function setProductsModified($productsModified)
    {
        $this->setData(SitemapInterface::PRODUCTS_MODIFIED, $productsModified);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductsUrl()
    {
        return $this->_getData(SitemapInterface::PRODUCTS_URL);
    }

    /**
     * @inheritdoc
     */
    public function setProductsUrl($productsUrl)
    {
        $this->setData(SitemapInterface::PRODUCTS_URL, $productsUrl);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLanding()
    {
        return $this->_getData(SitemapInterface::LANDING);
    }

    /**
     * @inheritdoc
     */
    public function setLanding($landing)
    {
        $this->setData(SitemapInterface::LANDING, $landing);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLandingPriority()
    {
        return $this->_getData(SitemapInterface::LANDING_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setLandingPriority($landingPriority)
    {
        $this->setData(SitemapInterface::LANDING_PRIORITY, $landingPriority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLandingFrequency()
    {
        return $this->_getData(SitemapInterface::LANDING_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setLandingFrequency($landingFrequency)
    {
        $this->setData(SitemapInterface::LANDING_FREQUENCY, $landingFrequency);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBrands()
    {
        return $this->_getData(SitemapInterface::BRANDS);
    }

    /**
     * @inheritdoc
     */
    public function setBrands($brands)
    {
        $this->setData(SitemapInterface::BRANDS, $brands);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBrandsPriority()
    {
        return $this->_getData(SitemapInterface::BRANDS_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setBrandsPriority($brandsPriority)
    {
        $this->setData(SitemapInterface::BRANDS_PRIORITY, $brandsPriority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBrandsFrequency()
    {
        return $this->_getData(SitemapInterface::BRANDS_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setBrandsFrequency($brandsFrequency)
    {
        $this->setData(SitemapInterface::BRANDS_FREQUENCY, $brandsFrequency);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExcludeUrls()
    {
        return $this->_getData(SitemapInterface::EXCLUDE_URLS);
    }

    /**
     * @inheritdoc
     */
    public function setExcludeUrls($excludeUrls)
    {
        $this->setData(SitemapInterface::EXCLUDE_URLS, $excludeUrls);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBlog()
    {
        return $this->_getData(SitemapInterface::BLOG);
    }

    /**
     * @inheritdoc
     */
    public function setBlog($blog)
    {
        $this->setData(SitemapInterface::BLOG, $blog);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBlogPriority()
    {
        return $this->_getData(SitemapInterface::BLOG_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setBlogPriority($blogPriority)
    {
        $this->setData(SitemapInterface::BLOG_PRIORITY, $blogPriority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBlogFrequency()
    {
        return $this->_getData(SitemapInterface::BLOG_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setBlogFrequency($blogFrequency)
    {
        $this->setData(SitemapInterface::BLOG_FREQUENCY, $blogFrequency);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNavigation()
    {
        return $this->_getData(SitemapInterface::NAVIGATION);
    }

    /**
     * @inheritdoc
     */
    public function setNavigation($navigation)
    {
        $this->setData(SitemapInterface::NAVIGATION, $navigation);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNavigationPriority()
    {
        return $this->_getData(SitemapInterface::NAVIGATION_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setNavigationPriority($navigationPriority)
    {
        $this->setData(SitemapInterface::NAVIGATION_PRIORITY, $navigationPriority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNavigationFrequency()
    {
        return $this->_getData(SitemapInterface::NAVIGATION_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setNavigationFrequency($navigationFrequency)
    {
        $this->setData(SitemapInterface::NAVIGATION_FREQUENCY, $navigationFrequency);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFaq()
    {
        return $this->_getData(SitemapInterface::FAQ);
    }

    /**
     * @inheritdoc
     */
    public function setFaq($faq)
    {
        $this->setData(SitemapInterface::FAQ, $faq);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFaqPriority()
    {
        return $this->_getData(SitemapInterface::FAQ_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setFaqPriority($faqPriority)
    {
        $this->setData(SitemapInterface::FAQ_PRIORITY, $faqPriority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFaqFrequency()
    {
        return $this->_getData(SitemapInterface::FAQ_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setFaqFrequency($faqFrequency)
    {
        $this->setData(SitemapInterface::FAQ_FREQUENCY, $faqFrequency);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDateFormat()
    {
        return $this->_getData(SitemapInterface::DATE_FORMAT);
    }

    /**
     * @inheritdoc
     */
    public function setDateFormat($dateFormat)
    {
        $this->setData(SitemapInterface::DATE_FORMAT, $dateFormat);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExcludeOutOfStock()
    {
        return $this->_getData(SitemapInterface::EXCLUDE_OUT_OF_STOCK);
    }

    /**
     * @inheritdoc
     */
    public function setExcludeOutOfStock($excludeOutOfStock)
    {
        $this->setData(SitemapInterface::EXCLUDE_OUT_OF_STOCK, $excludeOutOfStock);

        return $this;
    }
}
