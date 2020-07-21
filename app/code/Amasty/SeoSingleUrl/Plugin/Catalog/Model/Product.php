<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Plugin\Catalog\Model;

use Amasty\SeoSingleUrl\Model\Source\Type;
use Magento\Catalog\Model\Product as MagentoProduct;

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

    public function aroundGetRequestPath(
        MagentoProduct $product,
        \Closure $proceed
    ) {
        $type = $this->helper->getModuleConfig('general/product_url_type');
        $result = '';

        if ($type !== Type::DEFAULT_RULES && $product->getShouldGenerateSeoUrl()) {
            $result = $this->helper->getSeoUrl($product, $product->getStoreId());
        }

        if (!$result) {
            $result = $proceed();
        }

        return  $result;
    }
}
