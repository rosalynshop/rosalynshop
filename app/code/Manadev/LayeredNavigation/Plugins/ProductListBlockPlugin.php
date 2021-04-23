<?php

namespace Manadev\LayeredNavigation\Plugins;

use Magento\Catalog\Block\Product\ListProduct;

class ProductListBlockPlugin
{
    public function aroundToHtml(ListProduct $subject, callable $proceed, ...$args) {
        if ($subject->getData('manadev_hide')) {
            return '<div id="mana_ajax_wrapper_category_products_list"></div>';
        }

        return $proceed(...$args);
    }


}
