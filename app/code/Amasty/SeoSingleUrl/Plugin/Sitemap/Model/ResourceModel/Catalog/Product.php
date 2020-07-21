<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Plugin\Sitemap\Model\ResourceModel\Catalog;

use Amasty\SeoSingleUrl\Model\Source\Type;
use Magento\Sitemap\Model\ResourceModel\Catalog\Product as MagentoProduct;

class Product
{
    /**
     * @var \Amasty\SeoSingleUrl\Helper\Data
     */
    private $helper;

    public function __construct(
        \Amasty\SeoSingleUrl\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function afterGetCollection(
        MagentoProduct $subject,
        $result
    ) {
        $type = $this->helper->getModuleConfig('general/product_url_type');

        if ($type !== Type::DEFAULT_RULES) {
            foreach ($result as $key => $product) {
                $newUrl = $this->helper->generateSeoUrl($product->getId(), $product->getStoreId());
                $product->setData('url', $newUrl);
                $result[$key] = $product;
            }
        }

        return  $result;
    }
}
