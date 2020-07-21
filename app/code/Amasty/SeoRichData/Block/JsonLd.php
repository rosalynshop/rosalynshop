<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Block;

use Amasty\SeoRichData\Helper\Category as CategoryHelper;
use Amasty\SeoRichData\Model\DataCollector;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\SeoRichData\Model\Source\Category\Description as DescriptionSource;
use Amasty\SeoRichData\Helper\Config as ConfigHelper;

class JsonLd extends AbstractBlock
{
    /**
     * @var DataCollector
     */
    protected $dataCollector;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Core registry
     *
     * @var CategoryHelper
     */
    protected $categoryHelper = null;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Amasty\SeoRichData\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    private $layerResolver;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        DataCollector $dataCollector,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        CategoryHelper $categoryHelper,
        EncoderInterface $jsonEncoder,
        \Magento\Framework\View\Page\Config $pageConfig,
        ConfigHelper $configHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->coreRegistry = $coreRegistry;
        $this->jsonEncoder = $jsonEncoder;
        $this->dataCollector = $dataCollector;
        $this->storeManager = $storeManager;
        $this->categoryHelper = $categoryHelper;
        $this->pageConfig = $pageConfig;
        $this->configHelper = $configHelper;
        $this->layerResolver = $layerResolver;
    }

    protected function prepareData()
    {
        $data = [];

        $this->addBreadcrumbsData($data);
        $this->addWebsiteName($data);
        $this->addOrganizationData($data);
        $this->addCategoryData($data);
        $this->addSearchData($data);
        $this->addSocialProfiles($data);

        return $data;
    }

    protected function addWebsiteName(&$data)
    {
        if (!$this->configHelper->forWebsiteEnabled()) {
            return;
        }

        $name = $this->configHelper->getWebsiteName();

        if ($name) {
            $this->addWebsiteData($data);
            $data['website']['name'] = $name;
        }
    }

    protected function addBreadcrumbsData(&$data)
    {
        $breadcrumbs = $this->dataCollector->getData('breadcrumbs');
        if (is_array($breadcrumbs)) {
            $items = [];
            $position = 0;
            foreach ($breadcrumbs as $breadcrumb) {
                if (!$breadcrumb['link']) {
                    continue;
                }

                $items []= [
                    '@type' => 'ListItem',
                    'position' => ++$position,
                    'item' => [
                        '@id' => $breadcrumb['link'],
                        'name' => $breadcrumb['label']
                    ]
                ];
            }

            if (count($items) > 0) {
                if ($this->configHelper->sliceBreadcrumbs()) {
                    $items = array_slice($items, -1, 1);
                    if (isset($items[0])) {
                        $items[0]['position'] = 1;
                    }

                }

                $data['breadcrumbs'] = [
                    '@context'        => 'http://schema.org',
                    '@type'           => 'BreadcrumbList',
                    'itemListElement' => $items
                ];
            }
        }
    }

    protected function addOrganizationData(&$data)
    {
        if (!$this->configHelper->forOrganizationEnabled()) {
            return;
        }

        $data['organization'] = [
            '@context' => 'http://schema.org',
            '@type' => 'Organization',
            'url' => $this->_urlBuilder->getBaseUrl()
        ];

        if ($name = $this->configHelper->getOrganizationName()) {
            $data['organization']['name'] = $name;
        }

        if ($logoUrl = $this->configHelper->getOrganizationLogo()) {
            $data['organization']['logo'] = $logoUrl;
        }

        if ($description = $this->configHelper->getOrganizationDescription()) {
            $data['organization']['description'] = $description;
        }

        foreach ($this->configHelper->getOrganizationContacts() as $contactType => $contact) {
            $data['organization']['contactPoint'][] = [
                "@type" => "ContactPoint",
                "telephone" => $contact,
                "contactType" => str_replace('_', ' ', $contactType)
            ];
        }

        if ($country = $this->configHelper->getCountryName()) {
            $data['organization']['address']['addressCountry'] = $country;
        }

        if ($postalCode = $this->configHelper->getPostalCode()) {
            $data['organization']['address']['postalCode'] = $postalCode;
        }

        if ($region = $this->configHelper->getOrganizationRegion()) {
            $data['organization']['address']['addressRegion'] = $region;
        }

        if ($city = $this->configHelper->getOrganizationCity()) {
            $data['organization']['address']['addressLocality'] = $city;
        }
    }

    protected function addCategoryData(&$data)
    {
        if (!$this->configHelper->forCategoryEnabled()) {
            return;
        }

        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->coreRegistry->registry('current_category');

        if (!$category) {
            return;
        }

        if ('category' != $this->_request->getControllerName()) {
            return;
        }

        $data['category'] = $this->generateProductsInfo();
    }

    protected function addSearchData(&$data)
    {
        if (!$this->configHelper->forSearchEnabled()) {
            return;
        }
        $this->addWebsiteData($data);
        $data['website']['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => $this->_urlBuilder->getUrl('catalogsearch/result') . "?q={search_term_string}",
            'query-input' => 'required name=search_term_string'
        ];
    }

    protected function addWebsiteData(&$data)
    {
        if (isset($data['website'])) {
            return;
        }

        $data['website'] = [
            '@context' => 'http://schema.org',
            '@type' => 'WebSite',
            'url' => $this->_urlBuilder->getBaseUrl()
        ];
    }

    protected function _toHtml()
    {
        $data = $this->prepareData();

        $result = '';
        foreach ($data as $section) {
            $result .= "<script type=\"application/ld+json\">{$this->jsonEncoder->encode($section)}</script>";
        }

        return $result;
    }

    /**
     * Add person information
     *
     * @param $data
     */
    private function addSocialProfiles(&$data)
    {
        if ($this->configHelper->forSocialEnabled()
            && $this->configHelper->forOrganizationEnabled()
        ) {
            foreach ($this->configHelper->getSocialLinks() as $socialLink) {
                $data['organization']['sameAs'][] = $socialLink;
            }
        }
    }

    /**
     * @return array
     */
    private function generateProductsInfo()
    {
        $productCollection = $this->layerResolver->get()->getProductCollection();
        $productsInfo = [];
        /** @var \Amasty\SeoRichData\Block\Product $productBlock */
        $productBlock = $this->getLayout()->createBlock(
            \Amasty\SeoRichData\Block\Product::class
        );
        foreach ($productCollection as $product) {
            $productBlock->setProduct($product);
            $productInfo = $productBlock->getResultArray();
            $productsInfo[] = $productInfo;
        }

        return $productsInfo;
    }
}
