<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Api;

interface SitemapInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const TITLE = 'title';
    const FOLDER_NAME = 'folder_name';
    const MAX_ITEMS = 'max_items';
    const MAX_FILE_SIZE = 'max_file_size';
    const TYPE = 'type';
    const LAST_RUN = 'last_run';
    const STORE_ID = 'store_id';
    const CATEGORIES = 'categories';
    const CATEGORIES_MODIFIED = 'categories_modified';
    const CATEGORIES_THUMBS = 'categories_thumbs';
    const CATEGORIES_CAPTIONS = 'categories_captions';
    const CATEGORIES_PRIORITY = 'categories_priority';
    const CATEGORIES_FREQUENCY = 'categories_frequency';
    const PAGES = 'pages';
    const PAGES_PRIORITY = 'pages_priority';
    const PAGES_FREQUENCY = 'pages_frequency';
    const PAGES_MODIFIED = 'pages_modified';
    const EXCLUDE_CMS_ALIASES = 'exclude_cms_aliases';
    const EXTRA = 'extra';
    const EXTRA_PRIORITY = 'extra_priority';
    const EXTRA_FREQUENCY = 'extra_frequency';
    const EXTRA_LINKS = 'extra_links';
    const PRODUCTS = 'products';
    const PRODUCTS_THUMBS = 'products_thumbs';
    const PRODUCTS_CAPTIONS = 'products_captions';
    const PRODUCTS_CAPTIONS_TEMPLATE = 'products_captions_template';
    const PRODUCTS_PRIORITY = 'products_priority';
    const PRODUCTS_FREQUENCY = 'products_frequency';
    const PRODUCTS_MODIFIED = 'products_modified';
    const PRODUCTS_URL = 'products_url';
    const LANDING = 'landing';
    const LANDING_PRIORITY = 'landing_priority';
    const LANDING_FREQUENCY = 'landing_frequency';
    const BRANDS = 'brands';
    const BRANDS_PRIORITY = 'brands_priority';
    const BRANDS_FREQUENCY = 'brands_frequency';
    const EXCLUDE_URLS = 'exclude_urls';
    const BLOG = 'blog';
    const BLOG_PRIORITY = 'blog_priority';
    const BLOG_FREQUENCY = 'blog_frequency';
    const NAVIGATION = 'navigation';
    const NAVIGATION_PRIORITY = 'navigation_priority';
    const NAVIGATION_FREQUENCY = 'navigation_frequency';
    const DATE_FORMAT = 'date_format';
    const EXCLUDE_OUT_OF_STOCK = 'exclude_out_of_stock';
    const FAQ = 'faq';
    const FAQ_PRIORITY = 'faq_priority';
    const FAQ_FREQUENCY = 'faq_frequency';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getFolderName();

    /**
     * @param string $folderName
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setFolderName($folderName);

    /**
     * @return int
     */
    public function getMaxItems();

    /**
     * @param int $maxItems
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setMaxItems($maxItems);

    /**
     * @return int
     */
    public function getMaxFileSize();

    /**
     * @param int $maxFileSize
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setMaxFileSize($maxFileSize);

    /**
     * @return int
     */
    public function getType();

    /**
     * @param int $type
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getLastRun();

    /**
     * @param string $lastRun
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setLastRun($lastRun);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getCategories();

    /**
     * @param int $categories
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setCategories($categories);

    /**
     * @return int
     */
    public function getCategoriesModified();

    /**
     * @param int $categoriesModified
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setCategoriesModified($categoriesModified);

    /**
     * @return int
     */
    public function getCategoriesThumbs();

    /**
     * @param int $categoriesThumbs
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setCategoriesThumbs($categoriesThumbs);

    /**
     * @return int
     */
    public function getCategoriesCaptions();

    /**
     * @param int $categoriesCaptions
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setCategoriesCaptions($categoriesCaptions);

    /**
     * @return string
     */
    public function getCategoriesPriority();

    /**
     * @param string $categoriesPriority
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setCategoriesPriority($categoriesPriority);

    /**
     * @return string
     */
    public function getCategoriesFrequency();

    /**
     * @param string $categoriesFrequency
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setCategoriesFrequency($categoriesFrequency);

    /**
     * @return int
     */
    public function getPages();

    /**
     * @param int $pages
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setPages($pages);

    /**
     * @return string
     */
    public function getPagesPriority();

    /**
     * @param string $pagesPriority
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setPagesPriority($pagesPriority);

    /**
     * @return string
     */
    public function getPagesFrequency();

    /**
     * @param string $pagesFrequency
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setPagesFrequency($pagesFrequency);

    /**
     * @return int
     */
    public function getPagesModified();

    /**
     * @param int $pagesModified
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setPagesModified($pagesModified);

    /**
     * @return string
     */
    public function getExcludeCmsAliases();

    /**
     * @param string $excludeCmsAliases
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setExcludeCmsAliases($excludeCmsAliases);

    /**
     * @return int
     */
    public function getExtra();

    /**
     * @param int $extra
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setExtra($extra);

    /**
     * @return string
     */
    public function getExtraPriority();

    /**
     * @param string $extraPriority
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setExtraPriority($extraPriority);

    /**
     * @return string
     */
    public function getExtraFrequency();

    /**
     * @param string $extraFrequency
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setExtraFrequency($extraFrequency);

    /**
     * @return string
     */
    public function getExtraLinks();

    /**
     * @param string $extraLinks
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setExtraLinks($extraLinks);

    /**
     * @return int
     */
    public function getProducts();

    /**
     * @param int $products
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setProducts($products);

    /**
     * @return int
     */
    public function getProductsThumbs();

    /**
     * @param int $productsThumbs
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setProductsThumbs($productsThumbs);

    /**
     * @return int
     */
    public function getProductsCaptions();

    /**
     * @param int $productsCaptions
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setProductsCaptions($productsCaptions);

    /**
     * @return string
     */
    public function getProductsCaptionsTemplate();

    /**
     * @param string $productsCaptionsTemplate
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setProductsCaptionsTemplate($productsCaptionsTemplate);

    /**
     * @return string
     */
    public function getProductsPriority();

    /**
     * @param string $productsPriority
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setProductsPriority($productsPriority);

    /**
     * @return string
     */
    public function getProductsFrequency();

    /**
     * @param string $productsFrequency
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setProductsFrequency($productsFrequency);

    /**
     * @return int
     */
    public function getProductsModified();

    /**
     * @param int $productsModified
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setProductsModified($productsModified);

    /**
     * @return int
     */
    public function getProductsUrl();

    /**
     * @param int $productsUrl
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setProductsUrl($productsUrl);

    /**
     * @return int
     */
    public function getLanding();

    /**
     * @param int $landing
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setLanding($landing);

    /**
     * @return string
     */
    public function getLandingPriority();

    /**
     * @param string $landingPriority
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setLandingPriority($landingPriority);

    /**
     * @return string
     */
    public function getLandingFrequency();

    /**
     * @param string $landingFrequency
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setLandingFrequency($landingFrequency);

    /**
     * @return int
     */
    public function getBrands();

    /**
     * @param int $brands
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setBrands($brands);

    /**
     * @return string
     */
    public function getBrandsPriority();

    /**
     * @param string $brandsPriority
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setBrandsPriority($brandsPriority);

    /**
     * @return string
     */
    public function getBrandsFrequency();

    /**
     * @param string $brandsFrequency
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setBrandsFrequency($brandsFrequency);

    /**
     * @return string
     */
    public function getExcludeUrls();

    /**
     * @param string $excludeUrls
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setExcludeUrls($excludeUrls);

    /**
     * @return int
     */
    public function getBlog();

    /**
     * @param int $blog
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setBlog($blog);

    /**
     * @return string
     */
    public function getBlogPriority();

    /**
     * @param string $blogPriority
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setBlogPriority($blogPriority);

    /**
     * @return string
     */
    public function getBlogFrequency();

    /**
     * @param string $blogFrequency
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setBlogFrequency($blogFrequency);

    /**
     * @return int
     */
    public function getNavigation();

    /**
     * @param int $navigation
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setNavigation($navigation);

    /**
     * @return string
     */
    public function getNavigationPriority();

    /**
     * @param string $navigationPriority
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setNavigationPriority($navigationPriority);

    /**
     * @return string
     */
    public function getNavigationFrequency();

    /**
     * @param string $navigationFrequency
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setNavigationFrequency($navigationFrequency);

    /**
     * @return int
     */
    public function getFaq();

    /**
     * @param int $faq
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setFaq($faq);

    /**
     * @return string
     */
    public function getFaqPriority();

    /**
     * @param string $faq
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setFaqPriority($faq);

    /**
     * @return string
     */
    public function getFaqFrequency();

    /**
     * @param string $faqFrequency
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setFaqFrequency($faqFrequency);


    /**
     * @return string
     */
    public function getDateFormat();

    /**
     * @param string $dateFormat
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setDateFormat($dateFormat);

    /**
     * @return int
     */
    public function getExcludeOutOfStock();

    /**
     * @param int $excludeOutOfStock
     *
     * @return \Amasty\XmlSitemap\Api\SitemapInterface
     */
    public function setExcludeOutOfStock($excludeOutOfStock);
}
