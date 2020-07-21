<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Plugin\Catalog\Model\Product;

use Amasty\SeoSingleUrl\Model\Source\Type;
use Magento\Catalog\Model\Product\Url as MagentoUrl;

class Url
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

    public function beforeGetUrl(
        MagentoUrl $subject,
        $product,
        $params = []
    ) {
        $type = $this->helper->getModuleConfig('general/product_url_type');

        if ($this->helper->getModuleConfig('general/product_use_categories') && $type !== Type::DEFAULT_RULES) {
            $product->setShouldGenerateSeoUrl(true);
        }

        return [$product, $params];
    }
}
