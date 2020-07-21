<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoHtmlSitemap
 */


namespace Amasty\SeoHtmlSitemap\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Data\Collection;

class Renderer extends AbstractHelper
{
    protected static $_rootId        = null;

    protected static $_deployedArray = [];

    protected $_storeManager;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function renderTree($collection, $level = 0, $isTree = true)
    {
        if (self::$_rootId === null) {
            self::$_rootId = $this->_storeManager->getStore()->getRootCategoryId();
        }

        $html = '';
        $isRoot = self::$_rootId == $collection['category_id'];

        if (! $isRoot) {
            $level++;
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
            $url     = htmlspecialchars($baseUrl . $collection['url']);
            $padding = $collection['level'] * 30 - 30;
            $html .= '<li style="padding-left:' . $padding . 'px" class="tree-leaf">';
            $html .= '<a href="' . $url . '">';
            $html .= htmlspecialchars($collection['name']);
            $html .= '</a>';
        }

        if (! empty($collection['children'])) {
            if ($isTree) {
                foreach ($collection['children'] as $children) {
                    $html .= $this->renderTree($children, $level);
                }
            } else {
                foreach ($collection['children'] as $children) {
                    $html .= $this->renderTree($children, $level, false);
                }
            }
        }

        if (! $isRoot) {
            $html .= '</li>';
        }

        return $html;
    }

    public function renderArrayChunks($collection, $type, $columnSize = 1, $isTree = false)
    {
        $data        = $this->_getArrayInfo($collection, $columnSize);
        $columnWidth = 100 / $columnSize;

        $html = '<div class="am-sitemap-wrap am-clearfix">';
        foreach ($data['resultArray'] as $group) {
            $html .= '<div class="am-sitemap-column" style="width:' . $columnWidth . '%;">';
            $html .= '<div class="am-sitemap-cell">' . ($isTree ? '' : '<ul class="am-sitemap-list">');

            foreach ($group as $item) {
                $html .= $this->_renderFunction($type, $item);
            }
            $html .= ($isTree ? '' : '</ul>') . '</div></div>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * @param $array
     * @param int $columnSize
     * @return array
     */
    protected function _getArrayInfo($array, $columnSize = 1)
    {
        if ($array instanceof Collection) {
            $array = $array->getItems();
        } elseif (isset($array[0]['category_id'])) {
            $array = $this->_getTreeItems($array);
        }

        $columnSize = (int) $columnSize < 1 ? 1 : $columnSize;
        $chunkSize  = floor(count($array) / $columnSize) + (count($array) % $columnSize > 0 ? 1 : 0);

        $resultArray = array_chunk($array, $chunkSize);

        return [
            'rowsCount'    => $chunkSize,
            'columnsCount' => count($resultArray),
            'resultArray'  => $resultArray
        ];
    }

    protected function _getTreeItems($array)
    {
        self::$_deployedArray = [];
        foreach ($array as $key => $item) {
            $this->_getSubLevel($item);
        }

        return self::$_deployedArray;
    }

    /**
    recursive function for get the full tree of categories
     */
    protected function _getSubLevel($item)
    {
        $currentItem = $item;
        if (isset($currentItem['children']) && count($currentItem['children']) > 0) {
            unset($currentItem['children']);
            self::$_deployedArray[] = $currentItem;
            foreach ($item['children'] as $child) {
                $this->_getSubLevel($child);
            }
        } else {
            self::$_deployedArray[] = $currentItem;
        }

        return true;
    }

    protected function _renderFunction($type, $item)
    {
        if ($type == 'product_split') {
            $html = '<dl><dt>' . $item['letter'] . '</dt><dd><ul>';
            foreach ($item['items'] as $product) {
                $html .= '<li><a href="' . htmlspecialchars($product->getProductUrl()) . '">' .
                    htmlspecialchars($product->getName()) .
                    '</a></li>';
            }
            $html .= '</ul></dd></dl>';

            return $html;
        }

        if ($type == 'product') {
            return '<li><a href="' . htmlspecialchars($item->getProductUrl()) . '">' .
                htmlspecialchars($item->getName()) .
                '</a></li>';
        }

        if ($type == 'categories_list') {
            return '<li style="margin-left: 2em;"><a href="' . htmlspecialchars($item->getUrl()) . '">' .
                htmlspecialchars($item->getName()) .
                '</a></li>';
        }

        if ($type == 'categories_tree') {
            return $this->renderTree($item);
        }

        if ($type == 'pages') {
            return '<li><a href="' . htmlspecialchars($item['url']) . '">' .
                htmlspecialchars($item['title']) .
                '</a></li>';
        }

        if ($type == 'links') {
            return "<li><a href='" . $item['url'] . "'>" . $item['title'] . "</a></li>";
        }

        if ($type == 'landing_pages') {
            return "<li><a href='" . htmlspecialchars($item['url']) . "'>" . $item['title'] . "</a></li>";
        }
    }

}