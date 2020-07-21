<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Plugin\SeoRichData\Block;

class Product
{
    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $helper;

    /**
     * @var null|string
     */
    private $value = null;

    public function __construct(\Amasty\Meta\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Amasty\SeoRichData\Block\Product $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param string $key
     *
     * @return array
     */
    public function beforeGetMetaData($subject, $product, $key)
    {
        if ($product && $key) {
            $metaData = $this->helper->observeProductPage($product, false);
            $this->value = isset($metaData[$key]) ? $metaData[$key] : '';
        }

        return [$product, $key];
    }

    /**
     * @param \Amasty\SeoRichData\Block\Product $subject
     * @param string $result
     *
     * @return string
     */
    public function afterGetMetaData($subject, $result)
    {
        if ($this->value !== null) {
            $result = $this->value;
            $this->value = null;
        }

        return $result;
    }
}
