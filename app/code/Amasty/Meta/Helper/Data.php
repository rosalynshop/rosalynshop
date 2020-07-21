<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const ROBOTS_INDEX_FOLLOW     = 1;
    const ROBOTS_NOINDEX_FOLLOW   = 2;
    const ROBOTS_INDEX_NOFOLLOW   = 3;
    const ROBOTS_NOINDEX_NOFOLLOW = 4;
    const CONFIG_MAX_META_DESCRIPTION = 'ammeta/general/max_meta_description';
    const CONFIG_MAX_META_TITLE       = 'ammeta/general/max_meta_title';
    const DEFAULT_CHARSET = 'utf8';

    /** @var \Amasty\Meta\Model\Config */
    protected $_configByUrl = null;

    protected $_cache;
    protected $_entityCollection = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $http;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Amasty\Meta\Model\Config
     */
    private $metaConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $_escaper;

    /**
     * Data constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\App\Request\Http $http
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Amasty\Meta\Model\Config $metaConfig
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\App\Request\Http $http,
        \Magento\Catalog\Model\Category $category,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Meta\Model\Config $metaConfig,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Escaper $_escaper
    ) {
        $this->storeManager = $storeManagerInterface;
        $this->http = $http;
        $this->priceCurrency = $priceCurrency;
        $this->category = $category;
        $this->catalogHelper = $catalogHelper;
        $this->registry = $registry;
        $this->metaConfig = $metaConfig;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context);
        $this->_escaper = $_escaper;
    }

    /**
     * @param $configPath
     * @return mixed
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get config by url
     *
     * @return \Amasty\Meta\Model\Config
     */
    public function getMetaConfigByUrl($path = '')
    {
        if ($this->_configByUrl === null) {
            $storeId = $this->storeManager->getStore()->getId();
            $urls = $path ? [$path] : [$this->http->getRequestUri(), $this->http->getOriginalPathInfo()];
            $data = $this->metaConfig->getConfigByUrl($urls, $storeId);

            if (!empty($data)) {
                $data = $data->getData();
                $this->_configByUrl = [];

                if ($data) {
                    $customUrlMapping = $this->getUrlColumnsMapping();

                    foreach ($data as $item) {
                        if (isset($item['config_id'])) {
                            $applied = $this->registry->registry('ammeta_applied_rule');
                            if (!is_array($applied)) {
                                $applied = [];
                            }
                            $applied[] = __('Template (%1) #%2', __('Url'), $item['config_id']);
                            $this->registry->unregister('ammeta_applied_rule');
                            $this->registry->register('ammeta_applied_rule', $applied);
                        }

                        foreach ($customUrlMapping as $attrCode => $column) {
                            if (! isset($this->_configByUrl[$attrCode])
                                && !empty($item[$column])
                                && trim($item[$column]) != ''
                            ) {
                                if ($column == 'custom_robots') {
                                    foreach ($this->getRobotOptions() as $itemRobot) {
                                        if ($itemRobot['value'] == $item[$column]) {
                                            $item[$column] = $itemRobot['label'];
                                            break;
                                        }
                                    }
                                }

                                if ($column == 'custom_meta_description') {
                                    $item[$column] = mb_substr(
                                        $item[$column],
                                        0,
                                        $this->getMaxMetaDescriptionLength(),
                                        self::DEFAULT_CHARSET
                                    );
                                }

                                if ($column == 'custom_meta_title') {
                                    $item[$column] = mb_substr(
                                        $item[$column],
                                        0,
                                        $this->getMaxMetaTitleLength(),
                                        self::DEFAULT_CHARSET
                                    );
                                }

                                $this->_configByUrl[$attrCode] = $item[$column];
                            }
                        }
                    }
                }
            } else {
                $this->_configByUrl = false;
            }
        }

        return $this->_configByUrl;
    }

    /**
     * Parses template wth optional parts, uses _parseAttributes
     * @param $tpl
     * @param bool $isUrl
     * @return mixed|null|string|string[]
     */
    public function parse($tpl, $isUrl = false)
    {
        // replace attribute values if possible
        $tpl = $this->_parseAttributes($tpl, $isUrl);

        // handle optional parts
        $tpl = preg_replace_callback(
            '/\[.*?\]/',
            function ($m) {
                if (strpos($m[0], "}") !== false) {
                    return "";
                }

                return substr($m[0], 1, -1);
            },
            $tpl
        );

        // remove non-processed variables
        $tpl = preg_replace('/{([a-z\_\|0-9]+)}/', '', $tpl);

        return $tpl;
    }

    /**
     *  Parses template and insert attribute values
     *
     * @param $tpl
     *
     * @return mixed
     */
    protected function _parseAttributes($tpl, $isUrl)
    {
        $vars = [];
        preg_match_all('/{([a-z\_\|0-9]+)}/', $tpl, $vars);
        if (! $vars[1]) {
            return $tpl;
        }
        $vars = $vars[1];

        foreach ($vars as $codes) {
            $value = '';
            foreach (explode('|', $codes) as $code) {
                foreach ($this->_entityCollection as $object) {
                    $value = $this->_getValue($object, $code, $isUrl);
                    if ($value) {
                        break 2; // we have found the first non-empty occurense.
                    }
                }
            }
            if ($value) {
                $tpl = str_replace('{' . $codes . '}', $value, $tpl);
            }
        }

        return $tpl;
    }

    /**
     * Gets attribute value by its code. Support custom params, see manual for details
     *
     * @param $p
     * @param $code
     *
     * @return mixed|null|string
     */
    protected function _getValue($p, $code, $isUrl)
    {
        $store = null;

        if ($p instanceof \Magento\Catalog\Model\Product || $p instanceof \Magento\Catalog\Model\Category) {
            $value = $this->_getValueByProduct($p, $code);
            $store = $this->storeManager->getStore($p->getStoreId());
        } else {
            $value = $p->getData($code);
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string)$value;
        }

        if (!is_string($value) && !is_numeric($value)) {
            return '';
        }

        // remove tags
        $value = strip_tags($value);
        // remove spases
        $value = preg_replace('/\r?\n/', ' ', $value);
        $value = preg_replace('/\s{2,}/', ' ', $value);

        if ($isUrl) {
            $value = $this->transliterate($value);
            $value = str_replace('+', 'plus', $value);
            $value = preg_replace('#[^\w\s\d-_.~/]#', '', $value);
        } else {
            // convert possible special codes like '<' to safe html codes
            $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
            $value = htmlspecialchars($value);
        }
        // check if price = 0.00
        if ($store && $value === $this->priceCurrency->convert(0, true, false)) {
            $value = '';
        }

        return $value;
    }

    protected function _getProductCategory($p)
    {
        if ($p instanceof \Magento\Catalog\Model\Category) {
            return $p;
        }

        $collection = $p->getCategoryCollection()
            ->addIsActiveFilter();
        $collection->getSelect()->order('LENGTH(path) DESC');

        $categoryItems = $collection->load()->getIterator();
        $category      = current($categoryItems);
        if ($category) {
            $category = $this->category->load($category->getId());
            return $category;
        }

        return false;
    }

    protected function getCategoryValue($p)
    {
        $separator = (string) $this->getConfig('catalog/seo/title_separator');
        $separator = ' ' . $separator . ' ';
        $title     = [];

        if ($this->getConfig('ammeta/product/no_breadcrumbs')) {
            $categoryIds = $p->getCategoryIds();
            if ($categoryIds) {
                $categoryCollection = $this->category->getCollection();
                $categoryCollection->addIdFilter($categoryIds);
                $categoryCollection->addNameToResult();

                if ($categoryCollection->getSize() > 0) {
                    foreach ($categoryCollection as $cat) {
                        $title[] = $cat->getName();
                    }
                }
            }
            $value = join($separator, $title);
        } else {
            $path = $this->catalogHelper->getBreadcrumbPath();
            foreach ($path as $breadcrumb) {
                $title[] = $breadcrumb['label'];
            }
            array_pop($title);
            $value = join($separator, array_reverse($title));
        }

        return $value;
    }

    protected function _getValueByProduct($p, $code)
    {
        if ($p instanceof \Magento\Catalog\Model\Category) {
            switch ($code) {
                case 'meta_parent_category':
                    $value = $p->getParentCategory()->getName();
                    break;
                case 'store_view':
                    $value = $this->storeManager->getStore()->getName();
                    break;
                case 'store':
                    $value = $this->storeManager->getGroup()->getName();
                    break;
                case 'website':
                    $value = $this->storeManager->getWebsite()->getName();
                    break;
                default:
                    $value = $p->getData($code);
            }

            return $value;
        }

        $store = $this->storeManager->getStore($p->getStoreId());

        switch ($code) {
            case 'category':
                $value    = '';
                $category = $this->_getProductCategory($p);
                if ($category) {
                    $value = $category->getName();
                }
                break;
            case 'parent_category':
                $value    = '';
                /** @var \Magento\Catalog\Model\Category $category */

                $category = $this->_getProductCategory($p);

                if ($category) {
                    $value = $category->getParentCategory()->getName();
                }
                break;
            case 'categories':
                $value = $this->getCategoryValue($p);
                break;
            case 'store_view':
                $value = $store->getName();
                break;
            case 'store':
                $value = $store->getGroup()->getName();
                break;
            case 'website':
                $value = $store->getWebsite()->getName();
                break;
            case 'domain':
                $value = $this->_httpHeader->getHttpHost();
                break;
            case 'special_price':
                $value = $p->getPriceInfo()->getPrice('special_price')->getAmount()->getValue()
                 ?: $p->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
                break;
            case 'price':
            case 'startingfrom_price':
            case 'final_price':
                $value = $p->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
                break;
            case 'final_price_incl_tax':
                $price = $p->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue('tax'); //ensure tax elimination
                $value = $this->catalogHelper->getTaxPrice($p, $price, true);
                break;
            case 'startingto_price':
                $value = $p->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue();
                break;
            case 'current_page':
                $page  = $this->_getRequest()->getParam('p');
                $value = $page < 1 ? null : (int)$page;
                break;
            case 'category_ids':
                $value = implode(',', $p->getData($code));
                break;
            case 'custom_design':
                $value = '';
                break;
            default:
                $value = $this->getDefaultValue($p, $code);

        } // end switch

        return $value;
    }

    protected function getDefaultValue($p, $code)
    {
        if (!$code) {
            return '';
        }

        $value = $p->getData($code);
        if (is_numeric($value)) {
            // flat enabled
            if ($p->getData($code . '_value')) {
                $value = $p->getData($code . '_value');
            } else {
                $attr = $p->getResource()->getAttribute($code);
                if ($attr) { // type dropdown
                    $attr->setStoreId($p->getStoreId());
                    $optionText = $attr->getSource()->getOptionText($value);
                    $value      = $optionText ? $optionText : $value;
                }
            }
        } elseif (is_array($value)) {
            $value = implode(', ', $value);
        } elseif (preg_match('/^[0-9,]+$/', $value)) {
            $attr = $p->getResource()->getAttribute($code);
            if ($attr) {
                $ids   = explode(',', $value);
                $value = '';
                foreach ($ids as $id) {
                    $value .= $attr->getSource()->getOptionText($id) . ', ';
                }
                $value = substr($value, 0, - 2);
            }
        }

        return $value;
    }

    /**
     * Generates tree of all categories
     *
     * @return array sorted list category_id=>title
     */
    public function getTree($asHash = false)
    {
        $tree   = [];
        $pos = [];

        $collection = $this->category->getCollection()->addNameToResult();
        foreach ($collection as $cat) {
            $path = explode('/', $cat->getPath());
            if ($cat->getLevel()) {
                $tree[$cat->getId()] = [
                    'label' => str_repeat('--', $cat->getLevel()) . $cat->getName(),
                    'value' => $cat->getId(),
                    'path'  => $path,
                ];
            }
            $pos[$cat->getId()] = $cat->getPosition();
        }

        foreach ($tree as $catId => $cat) {
            $order = [];
            foreach ($cat['path'] as $id) {
                if (isset($pos[$id])) {
                    $order[] = $pos[$id];
                }
            }
            $tree[$catId]['order'] = $order;
        }

        usort($tree, [$this, 'compare']);
        if ($asHash) {
            $hash = [];
            foreach ($tree as $v) {
                $hash[$v['value']] = $v['label'];
            }
            $tree = $hash;
        }

        if (!empty($tree)) {
            reset($tree);
            $firstKey = key($tree);
            if ($asHash) {
                $firstElement = current($tree);
                $tree         = [0 => $firstElement] + $tree;
                unset($tree[$firstKey]);
            } else {
                $tree[$firstKey]['value'] = 1;
            }
        }

        return $tree;
    }

    /**
     * Compares category data. Must be public as used as a callback value
     *
     * @param array $a
     * @param array $b
     *
     * @return int 0, 1 , or -1
     */
    public function compare($a, $b)
    {
        foreach ($a['path'] as $i => $id) {
            if (! isset($b['path'][$i])) {
                // B path is shorther then A, and values before were equal
                return 1;
            }
            if ($id != $b['path'][$i]) {
                // compare category positions at the same level
                $p  = isset($a['order'][$i]) ? $a['order'][$i] : 0;
                $p2 = isset($b['order'][$i]) ? $b['order'][$i] : 0;

                return ($p < $p2) ? - 1 : 1;
            }
        }

        // B path is longer or equal then A, and values before were equal
        return ($a['value'] == $b['value']) ? 0 : - 1;
    }

    protected function _getMinimalPrice($product)
    {
        $minimalPrice = $this->catalogHelper->getTaxPrice($product, $product->getMinimalPrice(), true);
        if ($product->getTypeId() == 'configurable') {
            $associatedProducts = $product->getTypeInstance(true)->getUsedProducts($product);
            foreach ($associatedProducts as $item) {
                $temp = $this->catalogHelper->getTaxPrice($item, $item->getFinalPrice(), true);
                if ($minimalPrice == null || $temp < $minimalPrice) {
                    $minimalPrice = $temp;
                }
            }
        } elseif ($product->getTypeId() == 'bundle') {
            list($minimalPrice, $maximalPrice) = $product->getPriceModel()->getTotalPrices($product, null, null, false);
        }

        return $minimalPrice;
    }

    protected function _getMaximalPrice($product)
    {
        $maximalPrice = 0;
        if ($product->getTypeId() == 'configurable') {
            $associatedProducts = $product->getTypeInstance(true)->getUsedProducts($product);
            foreach ($associatedProducts as $item) {
                $temp = $this->catalogHelper->getTaxPrice($item, $item->getFinalPrice(), true);
                if ($qty = $item->getQty() * 1) {
                    $temp = $qty * $temp;
                }
                if ($maximalPrice < $temp) {
                    $maximalPrice = $temp;
                }
            }
        } elseif ($product->getTypeId() == 'bundle') {
            list($minimalPrice, $maximalPrice) = $product->getPriceModel()->getTotalPrices($product, null, null, false);
        }

        if (!$maximalPrice) {
            $maximalPrice = $this->catalogHelper->getTaxPrice($product, $product->getFinalPrice(), true);
        }

        return $maximalPrice;
    }

    public function getRobotOptions()
    {
        return [
            ['label' => 'INDEX, FOLLOW', 'value' => self::ROBOTS_INDEX_FOLLOW],
            ['label' => 'NOINDEX, FOLLOW', 'value' => self::ROBOTS_NOINDEX_FOLLOW],
            ['label' => 'INDEX, NOFOLLOW', 'value' => self::ROBOTS_INDEX_NOFOLLOW],
            ['label' => 'NOINDEX, NOFOLLOW', 'value' => self::ROBOTS_NOINDEX_NOFOLLOW]
        ];
    }

    public function getUrlColumnsMapping()
    {
        return [
            'meta_title'           => 'custom_meta_title',
            'meta_description'     => 'custom_meta_description',
            'meta_keyword'         => 'custom_meta_keywords',
            'meta_keywords'        => 'custom_meta_keywords',
            'meta_robots'          => 'custom_robots',
            'custom_canonical_url' => 'custom_canonical_url',
            'h1_tag'               => 'custom_h1_tag'
        ];
    }

    /**
     * @param $html
     * @param $newText
     *
     * @return $this
     */
    public function replaceH1Tag(&$html, $newText)
    {
        $html = preg_replace('/(\<h1.*?\>).+?(\<\/h1\>)/is', "\${1}" . $newText . '$2', $html);

        return $this;
    }

    /**
     * @param $html
     * @param array $attributes
     *
     * @return bool
     */
    public function replaceImageData(&$html, $attributes = [])
    {
        $domQuery = new \Zend_Dom_Query($html);
        $results  = $domQuery->query('.category-image img');

        if (! count($results)) {
            return false;
        }

        foreach ($results as $result) {
            foreach ($attributes as $tagName => $tagValue) {
                $result->setAttribute($tagName, $tagValue);
            }
            break;
        }

        $html = $results->getDocument()->saveHTML();
    }

    public function getMaxMetaDescriptionLength()
    {
        $value = (int) $this->getConfig(self::CONFIG_MAX_META_DESCRIPTION);

        return $value ? $value : 500;
    }

    public function getMaxMetaTitleLength()
    {
        $value = (int) $this->getConfig(self::CONFIG_MAX_META_TITLE);

        return $value ? $value : 250;
    }

    public function addEntityToCollection($object)
    {
        $this->_entityCollection[] = $object;
        return $this;
    }

    /**
     * @return $this
     */
    public function cleanEntityToCollection()
    {
        $this->_entityCollection = [];

        return $this;
    }

    public function transliterate($string)
    {
        $replace = [
            "а"=>"a","А"=>"a",
            "б"=>"b","Б"=>"b",
            "в"=>"v","В"=>"v",
            "г"=>"g","Г"=>"g",
            "д"=>"d","Д"=>"d",
            "е"=>"e","Е"=>"e",
            "ж"=>"zh","Ж"=>"zh",
            "з"=>"z","З"=>"z",
            "и"=>"i","И"=>"i",
            "й"=>"y","Й"=>"y",
            "к"=>"k","К"=>"k",
            "л"=>"l","Л"=>"l",
            "м"=>"m","М"=>"m",
            "н"=>"n","Н"=>"n",
            "о"=>"o","О"=>"o",
            "п"=>"p","П"=>"p",
            "р"=>"r","Р"=>"r",
            "с"=>"s","С"=>"s",
            "т"=>"t","Т"=>"t",
            "у"=>"u","У"=>"u",
            "ф"=>"f","Ф"=>"f",
            "х"=>"h","Х"=>"h",
            "ц"=>"c","Ц"=>"c",
            "ч"=>"ch","Ч"=>"ch",
            "ш"=>"sh","Ш"=>"sh",
            "щ"=>"sch","Щ"=>"sch",
            "ъ"=>"","Ъ"=>"",
            "ы"=>"y","Ы"=>"y",
            "ь"=>"","Ь"=>"",
            "э"=>"e","Э"=>"e",
            "ю"=>"yu","Ю"=>"yu",
            "я"=>"ya","Я"=>"ya",
            "і"=>"i","І"=>"i",
            "ї"=>"yi","Ї"=>"yi",
            "є"=>"e","Є"=>"e",
            "Ä"=>"ae","ä"=>"ae",
            "Ü"=>"ue","ü"=>"ue",
            "Ö"=>"oe","ö"=>"oe",
            "ß"=>"ss"
        ];

        return iconv("UTF-8", "UTF-8//IGNORE", strtr($string, $replace));
    }

    /**
     * @param $categoryPaths
     * @param $keys
     * @param string $startPrefix
     * @param null $cacheKey
     *
     * @return array
     */
    public function _getConfigData($categoryPaths, $keys, $startPrefix = 'cat_', $cacheKey = null)
    {
        if ($cacheKey && isset($this->_cache[$cacheKey])) {
            return $this->_cache[$cacheKey];
        }

        $configData =  $this->metaConfig->getRecursionConfigData(
            $categoryPaths,
            $this->storeManager->getStore()->getId()
        );

        if (!$configData) {
            return [];
        }

        $resultData = [];
        if ($cacheKey) {
            $this->_cache[$cacheKey] = & $resultData;
        }

        foreach ($keys as $keyName => $key) {

            if (is_numeric($keyName)) {
                $keyName = $key;
            }

            foreach ($configData as $itemConfig) {
                if ($startPrefix == 'cat_') {
                    $prefix = '';
                } else {
                    $prefix = $itemConfig->getOrder() == 0 ? '' : 'sub_';
                }

                $prefix .= $startPrefix;

                if (! isset($resultData[$key]) && ! empty($itemConfig[$prefix . $keyName]) &&
                    trim(! empty($itemConfig[$prefix . $keyName])) != ''
                ) {

                    if ($key == 'meta_description') {
                        $itemConfig[$prefix . $keyName] =
                            mb_substr(
                                $itemConfig[$prefix . $keyName],
                                0,
                                $this->getMaxMetaDescriptionLength(),
                                self::DEFAULT_CHARSET
                            );
                    }

                    if ($key == 'meta_title') {
                        $itemConfig[$prefix . $keyName] =
                            mb_substr($itemConfig[$prefix . $keyName],
                                0,
                                $this->getMaxMetaTitleLength(),
                                self::DEFAULT_CHARSET
                            );
                    }

                    $resultData[$key] = $itemConfig[$prefix . $keyName];
                    break;
                }
            }
        }

        return $resultData;
    }

    public function getReplaceData($code)
    {
        $configFromUrl = $this->getMetaConfigByUrl();

        $forceOverwrite = false;

        $currentCategory = $this->registry->registry('current_category');
        $currentProduct = $this->registry->registry('current_product');

        $currentEntity = false;

        if (isset($currentCategory) && !isset($currentProduct)) {
            $currentEntity = $currentCategory;
            $forceOverwrite = $this->scopeConfig->isSetFlag('ammeta/cat/force');
        } elseif (isset($currentProduct)) {
            $currentEntity = $currentProduct;
            $forceOverwrite = $this->scopeConfig->isSetFlag('ammeta/product/force');
        }

        $replacedData = $this->registry->registry('ammeta_replaced_data');

        if ($currentEntity && trim($currentEntity->getData($code)) != ''
            && !$forceOverwrite
        ) {
            return false;
        }

        $data = '';

        if (!empty($configFromUrl[$code])) {
            $configFromUrl[$code] = $this->parse($configFromUrl[$code]);
            $data = $configFromUrl[$code];
        } elseif ($replacedData && isset($replacedData[$code])) {
            $data = $replacedData[$code];
        }

        $data = $this->escapeHtml($data);

        return $data;
    }

    /**
     * @param \Amasty\SeoRichData\Block\Product $product
     * @param bool $needToRegister
     *
     * @return array|bool
     */
    public function observeProductPage($product, $needToRegister = true)
    {
        if (!$product || !$this->getConfig('ammeta/product/enabled')  || !$product->getCategoryIds()) {
            return false;
        }

        $catPaths = [];

        $categories = $this->categoryCollectionFactory->create()->addFieldToFilter(
            'entity_id',
            $product->getCategoryIds()
        );

        foreach ($categories as $category) {
            $catPaths[] = array_reverse($category->getPathIds());
        }

        // product attribute => template name
        $attributes = [
            'meta_title' => 'meta_title',
            'meta_description' => 'meta_description',
            'meta_keywords' => 'meta_keywords',
            'short_description' => 'short_description',
            'description' => 'description',
            'h1_tag' => 'h1_tag'
        ];

        $configFromUrl = $this->getMetaConfigByUrl();
        $configData = $this->_getConfigData($catPaths, $attributes, 'product_', 'pr');
        $forceOverwrite = $this->scopeConfig->isSetFlag('ammeta/product/force');

        $resultData = [];
        foreach ($attributes as $attrCode) {
            if (!$forceOverwrite && $this->isProductAttribute($attrCode) && trim($product->getData($attrCode))) {
                continue;
            }

            $configItem = null;
            if (!empty($configFromUrl[$attrCode])) {
                $configItem = $configFromUrl[$attrCode];
            } elseif (!empty($configData[$attrCode])) {
                $configItem = $configData[$attrCode];
            }

            if ($configItem) {
                $this->addEntityToCollection($product);
                $tag = $this->parse($configItem);

                $max = (int) $this->getConfig('ammeta/general/max_' . $attrCode);
                if ($max) {
                    $tag = mb_substr($tag, 0, $max, Data::DEFAULT_CHARSET);
                }
                $resultData[$attrCode] = $tag;
            }
        }

        if ($needToRegister) {
            $this->registry->register('ammeta_replaced_data', $resultData);
        }

        return $resultData;
    }

    /**
     * @param string $attr
     *
     * @return bool
     */
    private function isProductAttribute($attr)
    {
        return in_array($attr, [
                'description',
                'short_description'
        ]);
    }

    /**
     * @param $string
     * @return string
     */
    public function escapeHtml($string)
    {
        $pattern = '/&#?[a-z]*[0-9]*;/';
        $regEx = '#<script(.*?)>(.*?)</script(.*?)>#is';
        $emoji = [];
        preg_match_all($pattern, $string, $emoji);
        if (array_key_exists(0, $emoji)) {
            $emoji = $emoji[0];
        }
        while (preg_match($regEx, $string)) {
            $string = preg_replace($regEx, '', $string);
        }
        $string = preg_split($pattern, $string);
        $result = '';
        foreach ($string as $key => $item) {
            $result .= str_replace('&', '&amp;', $item);
            if (array_key_exists($key, $emoji)) {
                $result .= $emoji[$key];
            }
        }

        return $result;
    }
}
