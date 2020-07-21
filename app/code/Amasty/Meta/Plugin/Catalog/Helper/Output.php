<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Plugin\Catalog\Helper;

class Output
{

    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    public function __construct(
        \Amasty\Meta\Helper\Data $data
    ) {
        $this->data = $data;
    }

    public function aroundProductAttribute(
        $subject,
        \Closure $proceed,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $result = $proceed($product, $attributeHtml, $attributeName);
        $replaced = false;

        if ($attributeName == 'short_description' ) {
            $replaced = $this->data->getReplaceData('short_description');
        } elseif( $attributeName == 'description') {
            $replaced = $this->data->getReplaceData('description');
        }
        if ($replaced) {
            $result = $replaced;
        }

        return $result;
    }

    public function aroundCategoryAttribute(
        $subject,
        \Closure $proceed,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $replaced = false;
        $result = $proceed($product, $attributeHtml, $attributeName);

        if ($attributeName == 'short_description' ) {
            $replaced = $this->data->getReplaceData('short_description');
        } elseif( $attributeName == 'description') {
            $replaced = $this->data->getReplaceData('description');
        }
        if ($replaced) {
            $result = $replaced;
        }

        return $result;
    }
}
