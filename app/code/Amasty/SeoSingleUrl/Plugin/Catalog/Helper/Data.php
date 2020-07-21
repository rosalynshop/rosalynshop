<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Plugin\Catalog\Helper;

use Amasty\SeoSingleUrl\Model\Source\Breadcrumb;
use Amasty\SeoSingleUrl\Model\Source\Type;
use Magento\Catalog\Helper\Data as MagentoData;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Data
{
    /**
     * @var \Amasty\SeoSingleUrl\Helper\Data
     */
    private $helper;

    /**
     * @var CollectionFactory
     */
    private $categoryFactoryCollection;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @var MagentoData
     */
    private $catalogData;

    public function __construct(
        \Amasty\SeoSingleUrl\Helper\Data $helper,
        CollectionFactory $categoryFactoryCollection,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->helper = $helper;
        $this->categoryFactoryCollection = $categoryFactoryCollection;
        $this->serializer = $serializer;
        $this->catalogData = $catalogData;
    }

    /**
     * @param MagentoData $subject
     * @param \Closure $proceed
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetBreadcrumbPath(
        MagentoData $subject,
        \Closure $proceed
    ) {
        $result = $this->getBreadcrumbsData($subject->getProduct());
        if (!$result) {
            $result = $proceed();
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\ViewModel\Product\Breadcrumbs $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetJsonConfigurationHtmlEscaped($subject, $result)
    {
        $breadcrumbData = $this->getBreadcrumbsData();
        if ($breadcrumbData && $result) {
            try {
                $result = $this->serializer->unserialize($result);
                array_pop($breadcrumbData);
                $result['breadcrumbs']['breadcrumbsData'] = array_values($breadcrumbData);
                $result = $this->serializer->serialize($result);
            } catch (\InvalidArgumentException $exception) {
                null;//do nothing - return result
            }
        }

        return $result;
    }

    /**
     * @param null|Product $product
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getBreadcrumbsData($product = null)
    {
        $type = $this->helper->getModuleConfig('general/breadcrumb');
        if ($product === null) {
            $product = $this->catalogData->getProduct();
        }
        $result = [];

        if ($type === Breadcrumb::CURRENT_URL
            && $product
            && $this->helper->isUseCategoriesPath()
            && $this->helper->getProductUrlType() !== Type::DEFAULT_RULES
        ) {
            $seoUrl = $this->helper->getSeoUrl($product, $product->getStoreId());
            $urlArray = explode('/', $seoUrl);
            array_pop($urlArray);

            if ($urlArray) {
                $storeId = $product->getStoreId();
                $breadcrumbsIds = $this->getBreadcrumbsPath(
                    $storeId,
                    end($urlArray),
                    $product->getCategoryIds()
                );

                $breadcrumbs = $this->categoryFactoryCollection->create()
                    ->setStore($storeId)
                    ->addNameToResult()
                    ->addAttributeToSelect('url_key')
                    ->addIdFilter($breadcrumbsIds);

                if (!empty(array_filter($breadcrumbsIds))) {
                    $breadcrumbs->getSelect()->order(new \Zend_Db_Expr('FIELD(entity_id,'
                        . implode(",", $breadcrumbsIds) . ')'));
                }

                foreach ($breadcrumbs as $breadcrumb) {
                    if (in_array($breadcrumb->getUrlKey(), $urlArray)) {
                        $result['category' . $breadcrumb->getId()] = [
                            'name' => 'category',
                            'label' => $breadcrumb->getName(),
                            'link' => $breadcrumb->getUrl(),
                            'title' => ''
                        ];
                    }
                }

                if ($product) {
                    $result['product'] = [
                        'name' => 'product',
                        'label' => $product->getName(),
                        'title' => ''
                    ];
                }
            }
        }

        return $result;
    }

    private function getBreadcrumbsPath($storeId, $urlKey, $availableIds)
    {
        $productCategory = $this->categoryFactoryCollection->create()
            ->setStore($storeId)
            ->addAttributeToFilter('url_key', $urlKey)
            ->addIdFilter($availableIds)
            ->addOrderField('level')
            ->setPageSize(1)
            ->getFirstItem();

        return explode('/', $productCategory->getPath());
    }
}
