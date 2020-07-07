<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\SmartMenu\Block;

use Magento\Customer\Model\Context;

class SmartMenu extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_cmsFilter;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_helperCategory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * Current category key
     *
     * @var string
     */
    protected $_currentCategoryKey;

    /**
     * @var array
     */
    protected $_catPosLevel = [];

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $flatState;

    /**
     * @var bolean
     */
    protected $_isTouch;

    /**
     * SmartMenu constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Helper\Category $helperCategory
     * @param \Magento\Cms\Model\Template\FilterProvider $cmsFilter
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Category $helperCategory,
        \Magento\Cms\Model\Template\FilterProvider $cmsFilter,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        array $data = []
    ) {
        $this->_helperCategory = $helperCategory;
        $this->_cmsFilter = $cmsFilter;
        $this->_categoryFactory = $categoryFactory;
        $this->_registry = $registry;
        $this->flatState = $flatState;
        $this->httpContext = $httpContext;
        $this->_catalogLayer = $layerResolver->get();
        parent::__construct($context, $data);
    }

    public function getCacheKeyInfo()
    {
        $shortCacheId = [
            'MAGEBIG_SMARTMENU',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(Context::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getCurrentCategoryKey(),
        ];
        $cacheId = $shortCacheId;

        $shortCacheId = array_values($shortCacheId);
        $shortCacheId = implode('|', $shortCacheId);
        $shortCacheId = md5($shortCacheId);

        $cacheId['category_path'] = $this->getCurrentCategoryKey();
        $cacheId['short_cache_id'] = $shortCacheId;

        return $cacheId;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentCategoryKey()
    {
        if (!$this->_currentCategoryKey) {
            $category = $this->_registry->registry('current_category');
            if ($category) {
                $this->_currentCategoryKey = $category->getPath();
            } else {
                $this->_currentCategoryKey = $this->_storeManager->getStore()->getRootCategoryId();
            }
        }

        return $this->_currentCategoryKey;
    }

    /**
     * Checkin activity of category
     *
     * @param   \Magento\Framework\DataObject $category
     * @return  bool
     */
    public function isCategoryActive($category)
    {
        if ($this->getCurrentCategory()) {
            return in_array($category->getId(), $this->getCurrentCategory()->getPathIds());
        }
        return false;
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->_catalogLayer->getCurrentCategory();
    }

    /**
     * @param $catId
     *
     * @return \Magento\Catalog\Model\Category
     */
    protected function _getCatData($catId)
    {
        $catModel = $this->_categoryFactory->create();

        return $catModel->load($catId);
    }

    /**
     * @param $block
     *
     * @return string
     * @throws \Exception
     */
    protected function _getStatic($block)
    {
        return $this->_cmsFilter->getBlockFilter()->filter(trim($block));
    }

    public function getHtml($catLevel = 0, $outerClass = '', $childWrapClass = '', $vertical = false)
    {
        if ($childWrapClass == 'nav-mobile' || $childWrapClass == 'cat-pagemenu') {
            $this->_isTouch = true;
        } else {
            $this->_isTouch = false;
        }
        $catActive = [];
        $catHelper = $this->_helperCategory;
        foreach ($catHelper->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $catActive[] = $child;
            }
        }
        $catActiveCount = count($catActive);
        $hascatActiveCount = ($catActiveCount > 0);

        if (!$hascatActiveCount) {
            return '';
        }

        $html = '';
        foreach ($catActive as $catInfo) {
            if ($this->_isTouch) {
                $html .= $this->_catSimpleHtml($catInfo, $catLevel, $outerClass, $childWrapClass, $vertical);
            } else {
                $catData = $this->_getCatData($catInfo->getId());
                $catStyle = $catData->getData('smartmenu_cat_style');

                if ($catStyle == 'dropdown_mega' || $catStyle == 'dropdown_group') {
                    $html .= $this->_catMegaHtml($catInfo, $catLevel, $outerClass, $childWrapClass, $vertical);
                } elseif ($catStyle == 'dropdown_simple') {
                    $html .= $this->_catSimpleHtml($catInfo, $catLevel, $outerClass, $childWrapClass);
                } else {
                    $html .= $this->_catSimpleHtml($catInfo, $catLevel, $outerClass,
                        'simple-dropdown ' . $childWrapClass);
                }
            }
        }

        return $html;
    }

    protected function _catMegaHtml($catInfo, $catLevel = 0, $outerClass = '', $childWrapClass = '')
    {
        $catData = $this->_getCatData($catInfo->getId());
        if (!$catInfo->getIsActive() || $catData->getData('include_in_menu') == 0) {
            return '';
        }
        $html = '';

        if ($this->flatState->isFlatEnabled() && $catInfo->getUseFlatResource()) {
            $children      = (array)$catInfo->getChildrenNodes();
        } else {
            $children      = $catInfo->getChildren();
        }

        $activeChildren = [];
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);
        $catStyle = $catData->getData('smartmenu_cat_style');
        if ($catData->getData('smartmenu_cat_position') == 'fullwidth') {
            $subwidth = '100%';
        } else {
            $subwidth = $catData->getData('smartmenu_cat_dropdown_width') ? $catData->getData('smartmenu_cat_dropdown_width') : '270px';
        }

        $showblock = $hasActiveChildren;

        if ($catLevel == 1) {
            if ($catData->getData('smartmenu_static_top') || $catData->getData('smartmenu_static_left') || $catData->getData('smartmenu_static_right') || $catData->getData('smartmenu_static_bottom')) {
                $showblock = true;
            }
        }

        // Custom URL
        $noclick = '';
        if ($custom_url = $catData->getData('smartmenu_cat_target')) {
            if ($custom_url === '#') {
                $cat_link = '#';
                $classes[] = 'no-click';
                $noclick = ' onclick="return false;"';
            } elseif ($custom_url = trim($custom_url)) {
                if (strpos($custom_url, 'http') === 0) {
                    $cat_link = $custom_url;
                } else {
                    $cat_link = $this->_storeManager->getStore()->getBaseUrl() . $custom_url;
                }
            } else {
                $cat_link = $catData->getUrl();
            }
        } else {
            $cat_link = $catData->getUrl();
        }

        $classes = [];
        $classes[] = 'level' . $catLevel;
        if ($catLevel == 1) {
            $classes[] = 'groups item';
        }
        $classes[] = 'nav-' . $this->_getCatPosition($catLevel);
        if ($this->isCategoryActive($catInfo) || ($custom_url && $this->getRequest()->getRouteName() === $custom_url)) {
            $classes[] = 'active';
        }
        $linkClass = '';
        if ($outerClass) {
            $classes[] = $outerClass;
            $linkClass = ' class="' . $outerClass . '"';
        }

        if ($hasActiveChildren && $catLevel >= 1) {
            $classes[] = 'mega_' . $catData->getData('smartmenu_cat_position');
            $classes[] = 'parent';
        }
        if ($hasActiveChildren && $catLevel == 0 && $showblock) {
            $classes[] = 'mega_' . $catData->getData('smartmenu_cat_position');
            $classes[] = 'parent';
        }

        $attributes = [];
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html .= $htmlLi;
        if ($catLevel == 1 && $showblock) {
            if ($catData->getData('smartmenu_static_top')) {
                $html .= '<div class="mbmenu-block mbmenu-block-level1-top std">';
                $html .= $this->_getStatic($catData->getData('smartmenu_static_top'));
                $html .= '</div>';
            }
        }
        $html .= '<a href="' . $cat_link . '"' . $linkClass . $noclick . '>';

        $iconHtml = $catData->getData('smartmenu_cat_icon');
        if (empty($iconHtml)) {
            $iconImage = $catData->getData('smartmenu_cat_imgicon');
            if (!empty($iconImage)) {
                $iconHtml = '<img alt="' . $catData->getData('name') . '" src="' . $this->_storeManager->getStore()->getBaseUrl() . 'pub/media/catalog/category/' . $iconImage . '">';
            }
        }
        $html .= $iconHtml;
        $labelCategory = $this->_getCatLabel($catData, $catLevel);
        if ($catLevel == 1) {
            $html .= '<span class="title_group">' . __($this->escapeHtml($catInfo->getName())) . $labelCategory . '</span>';
        } else {
            $html .= '<span>' . __($this->escapeHtml($catInfo->getName())) . $labelCategory . '</span>';
        }
        $html .= '</a>';
        if ($hasActiveChildren && $catLevel == 0) {
            $html .= '<i class="mbi mbi-ios-arrow-down"></i>';
        }

        if ($catLevel == 0) {
            $catStaticRight = $this->_getStatic($catData->getData('smartmenu_static_right'));
            $catStaticLeft = $this->_getStatic($catData->getData('smartmenu_static_left'));
            if ($catData->getData('smartmenu_block_right') || $catData->getData('smartmenu_block_left')) {
                $columns = $catData->getData('smartmenu_cat_column');
                $widthRight = $catData->getData('smartmenu_block_right');
                $widthLeft = $catData->getData('smartmenu_block_left');
            } else {
                if ($catData->getData('smartmenu_cat_column') == '') {
                    $columns = 3;
                } else {
                    $columns = $catData->getData('smartmenu_cat_column');
                }
                $widthRight = 1;
                $widthLeft = 1;
            }
            $goups = $widthRight + $widthLeft;
            if (empty($catStaticRight) || empty($catStaticLeft) || $catStyle == 'dropdown_group') {
                if (empty($catStaticRight)) {
                    $gridCount1 = 'col12-' . (12 - $widthLeft);
                    $gridCountLeft = 'col12-' . ($widthLeft);
                }
                if (empty($catStaticLeft)) {
                    $gridCount1 = 'col12-' . (12 - $widthRight);
                    $gridCountRight = 'col12-' . ($widthRight);
                }
                if (empty($catStaticRight) && empty($catStaticLeft) || $catStyle == 'dropdown_group') {
                    $gridCount1 = 'col12-12';
                }
            } elseif (!$hasActiveChildren) {
                $gridCountRight = 'col12-' . $widthRight;
                $gridCountLeft = 'col12-' . $widthLeft;
            } else {
                $grid = 12 - $goups;
                $gridCount1 = 'col12-' . ($grid);
                $gridCountRight = 'col12-' . ($widthRight);
                $gridCountLeft = 'col12-' . ($widthLeft);
            }
            $goups = $widthRight + $widthLeft;
        }

        $li = '';
        foreach ($activeChildren as $child) {
            $li .= $this->_catMegaHtml($child, ($catLevel + 1), $outerClass, $childWrapClass);
        }

        if ($childWrapClass && $showblock) {
            if ($catStyle == 'dropdown_group') {
                if ($catLevel == 1) {
                    $html .= '<div class="groups-wrapper">';
                    $html .= '<div class="show-sub-content">';
                } else {
                    $html .= '<div class="level' . $catLevel . ' simple-dropdown ' . $childWrapClass . ' show-sub" style="width: ' . $subwidth . ';height:auto;">';
                    $html .= '<div class="show-sub-content">';
                }
            } else {
                if ($catLevel == 1) {
                    $html .= '<div class="groups-wrapper">';
                    $html .= '<div class="show-sub-content">';
                } else {
                    $html .= '<div class="level' . $catLevel . ' ' . $childWrapClass . ' show-sub" style="width: ' . $subwidth . '; height:auto;">';
                    $html .= '<div class="show-sub-content">';
                }
            }
        }
        if ($catLevel == 0 && $showblock) {
            if ($catData->getData('smartmenu_static_top')) {
                $html .= '<div class="mbmenu-block mbmenu-block-top grid-full std">';
                $html .= $this->_getStatic($catData->getData('smartmenu_static_top'));
                $html .= '</div>';
            }
            if ($catStyle != 'dropdown_group') {
                if ($catData->getData('smartmenu_static_left') && $widthLeft) {
                    $html .= '<div class="menu-static-blocks mbmenu-block mbmenu-block-left ' . $gridCountLeft . '">';
                    $html .= $this->_getStatic($catData->getData('smartmenu_static_left'));
                    $html .= '</div>';
                }
            }
        }
        if (!empty($li) && $hasActiveChildren) {
            if ($catLevel == 0) {
                $colCenter = 'itemgrid itemgrid-' . $columns . 'col';
                $html .= '<div class="mbmenu-block mbmenu-block-center menu-items ' . $gridCount1 . ' ' . $colCenter . '">';
            }
            $html .= '<ul class="level' . $catLevel . '">';
            $html .= $li;
            $html .= '</ul>';
            if ($catLevel == 0) {
                $html .= '</div>';
            }
        }

        if ($catLevel == 0 && $showblock) {
            if ($catStyle != 'dropdown_group') {
                if ($catData->getData('smartmenu_static_right') && $widthRight) {
                    $html .= '<div class="menu-static-blocks mbmenu-block mbmenu-block-right ' . $gridCountRight . '">';
                    $html .= $this->_getStatic($catData->getData('smartmenu_static_right'));
                    $html .= '</div>';
                }
            }
            if ($catData->getData('smartmenu_static_bottom')) {
                $html .= '<div class="mbmenu-block mbmenu-block-bottom grid-full std">';
                $html .= $this->_getStatic($catData->getData('smartmenu_static_bottom'));
                $html .= '</div>';
            }
        }
        if ($childWrapClass && $showblock) {
            $html .= '</div>';
            $html .= '</div>';
        }

        if ($catLevel == 1 && $showblock) {
            if ($catData->getData('smartmenu_static_bottom')) {
                $html .= '<div class="mbmenu-block mbmenu-block-level1-top std">';
                $html .= $this->_getStatic($catData->getData('smartmenu_static_bottom'));
                $html .= '</div>';
            }
        }
        $html .= '</li>';

        return $html;
    }

    protected function _catSimpleHtml(
        $catInfo,
        $catLevel = 0,
        $outerClass = '',
        $childWrapClass = '',
        $vertical = false
    ) {
        if (!$catInfo->getIsActive()) {
            return '';
        }
        $catData = $this->_getCatData($catInfo->getId());
        if ($catData->getData('include_in_menu') == 0) {
            return '';
        }
        if ($vertical == true && $catData->getData('smartmenu_show_on_cat') == 0) {
            return '';
        }
        $html = '';

        if ($this->flatState->isFlatEnabled() && $catInfo->getUseFlatResource()) {
            $children      = (array)$catInfo->getChildrenNodes();
        } else {
            $children      = $catInfo->getChildren();
        }

        $activeChildren = [];
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);
        $catStyle = $catData->getData('smartmenu_cat_style');
        if ($catData->getData('smartmenu_cat_position') == 'fullwidth') {
            $subwidth = '100%';
        } else {
            $subwidth = $catData->getData('smartmenu_cat_dropdown_width') ? $catData->getData('smartmenu_cat_dropdown_width') : '270px';
        }

        $showblock = $hasActiveChildren;

        // Custom URL
        $noclick = '';
        if ($custom_url = $catData->getData('smartmenu_cat_target')) {
            if ($custom_url === '#') {
                $cat_link = '#';
                $classes[] = 'no-click';
                $noclick = ' onclick="return false;"';
            } elseif ($custom_url = trim($custom_url)) {
                if (strpos($custom_url, 'http') === 0) {
                    $cat_link = $custom_url;
                } else {
                    $cat_link = $this->_storeManager->getStore()->getBaseUrl() . $custom_url;
                }
            } else {
                $cat_link = $catData->getUrl();
            }
        } else {
            $cat_link = $catData->getUrl();
        }

        $classes = [];
        $classes[] = 'level' . $catLevel;
        if ($catLevel == 1) {
            $classes[] = 'item';
        }
        $classes[] = 'nav-' . $this->_getCatPosition($catLevel);
        // if ($this->isCategoryActive($catInfo) || ($custom_url && Mage::app()->getFrontController()->getRequest()->getRouteName() === $custom_url) || ($custom_url && Mage::getSingleton('cms/page')->getIdentifier() === $custom_url)) {
        if ($this->isCategoryActive($catInfo) || ($custom_url && $this->getRequest()->getRouteName() === $custom_url)) {
            $classes[] = 'active';
        }
        $linkClass = '';
        if ($outerClass) {
            $classes[] = $outerClass;
            $linkClass = ' class="' . $outerClass . '"';
        }

        if ($hasActiveChildren && $catLevel >= 1) {
            $classes[] = 'mega_' . $catData->getData('smartmenu_cat_position');
            $classes[] = 'parent';
        }
        if ($hasActiveChildren && $catLevel == 0 && $showblock && $this->_isTouch == false) {
            $classes[] = 'mega_' . $catData->getData('smartmenu_cat_position');
            $classes[] = 'parent';
        }

        $attributes = [];
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html .= $htmlLi;
        if ($catLevel == 1 && $showblock && $this->_isTouch == false) {
            if ($catData->getData('smartmenu_static_top')) {
                $html .= '<div class="mbmenu-block mbmenu-block-level1-top std">';
                $html .= $this->_getStatic($catData->getData('smartmenu_static_top'));
                $html .= '</div>';
            }
        }
        $labelCategory = $this->_getCatLabel($catData, $catLevel);
        $html .= '<a href="' . $cat_link . '"' . $linkClass . $noclick . '>';
        $html .= $catData->getData('smartmenu_cat_icon');
        $html .= '<span>' . __($this->escapeHtml($catInfo->getName())) . $labelCategory . '</span>';
        $html .= '</a>';
        if ($hasActiveChildren && $catLevel == 0 && $childWrapClass != 'nav-mobile') {
            $html .= '<i class="mbi mbi-ios-arrow-down"></i>';
        }

        if ($catLevel == 0 && $this->_isTouch == false) {
            $catStaticRight = $this->_getStatic($catData->getData('smartmenu_static_right'));
            $catStaticLeft = $this->_getStatic($catData->getData('smartmenu_static_left'));
            if ($catData->getData('smartmenu_block_right') || $catData->getData('smartmenu_block_left')) {
                $columns = $catData->getData('smartmenu_cat_column');
                $widthRight = $catData->getData('smartmenu_block_right');
                $widthLeft = $catData->getData('smartmenu_block_left');
            } else {
                if ($catData->getData('smartmenu_cat_column') == '') {
                    $columns = 4;
                } else {
                    $columns = $catData->getData('smartmenu_cat_column');
                }
                $widthRight = 1;
                $widthLeft = 1;
            }
            $goups = $widthRight + $widthLeft;
            if (empty($catStaticRight) || empty($catStaticLeft) || $catStyle == 'dropdown_simple') {
                if (empty($catStaticRight)) {
                    $gridCount1 = 'col12-' . (12 - $widthLeft);
                    $gridCountLeft = 'col12-' . ($widthLeft);
                }
                if (empty($catStaticLeft)) {
                    $gridCount1 = 'col12-' . (12 - $widthRight);
                    $gridCountRight = 'col12-' . ($widthRight);
                }
                if (empty($catStaticRight) && empty($catStaticLeft) || $catStyle == 'dropdown_simple') {
                    $gridCount1 = 'col12-12';
                }
            } elseif (!$hasActiveChildren) {
                $gridCountRight = 'col12-' . $widthRight;
                $gridCountLeft = 'col12-' . $widthLeft;
            } else {
                $grid = 12 - $goups;
                $gridCount1 = 'col12-' . ($grid);
                $gridCountRight = 'col12-' . ($widthRight);
                $gridCountLeft = 'col12-' . ($widthLeft);
            }
            $goups = $widthRight + $widthLeft;
        }

        $li = '';
        foreach ($activeChildren as $child) {
            $li .= $this->_catSimpleHtml($child, ($catLevel + 1), $outerClass, $childWrapClass);
        }

        if ($childWrapClass && $showblock && $this->_isTouch == false) {
            if ($catStyle == 'dropdown_simple') {
                $html .= '<div class="simple-dropdown ' . $childWrapClass . ' show-sub" style="width: ' . $subwidth . '; height:auto;">';
                $html .= '<div class="show-sub-content">';
            } else {
                $html .= '<div class="' . $childWrapClass . ' show-sub" style="width: ' . $subwidth . '; height:auto;">';
                $html .= '<div class="show-sub-content">';
            }
        }

        if ($catLevel == 0 && $showblock && $this->_isTouch == false) {
            if ($catData->getData('smartmenu_static_top')) {
                $html .= '<div class="mbmenu-block mbmenu-block-top grid-full std">';
                $html .= $this->_getStatic($catData->getData('smartmenu_static_top'));
                $html .= '</div>';
            }
            if ($catStyle != 'dropdown_simple') {
                if ($catData->getData('smartmenu_static_left') && $widthLeft) {
                    $html .= '<div class="menu-static-blocks mbmenu-block mbmenu-block-left ' . $gridCountLeft . '">';
                    $html .= $this->_getStatic($catData->getData('smartmenu_static_left'));
                    $html .= '</div>';
                }
            }
        }
        if (!empty($li) && $hasActiveChildren) {
            if ($catLevel == 0 && $this->_isTouch == false) {
                $colCenter = 'itemgrid itemgrid-' . $columns . 'col';
                $html .= '<div class="mbmenu-block mbmenu-block-center menu-items ' . $gridCount1 . ' ' . $colCenter . '">';
            }
            $html .= '<ul class="level' . $catLevel . '">';
            $html .= $li;
            $html .= '</ul>';
            if ($catLevel == 0 && $this->_isTouch == false) {
                $html .= '</div>';
            }
        }
        if ($catLevel == 0 && $showblock && $this->_isTouch == false) {
            if ($catStyle != 'dropdown_simple') {
                if ($catData->getData('smartmenu_static_right') && $widthRight) {
                    $html .= '<div class="menu-static-blocks mbmenu-block mbmenu-block-right ' . $gridCountRight . '">';
                    $html .= $this->_getStatic($catData->getData('smartmenu_static_right'));
                    $html .= '</div>';
                }
            }
            if ($catData->getData('smartmenu_static_bottom')) {
                $html .= '<div class="mbmenu-block mbmenu-block-bottom grid-full std">';
                $html .= $this->_getStatic($catData->getData('smartmenu_static_bottom'));
                $html .= '</div>';
            }
        }

        if ($childWrapClass && $showblock && $this->_isTouch == false) {
            $html .= '</div>';
            $html .= '</div>';
        }

        if ($catLevel == 1 && $showblock && $this->_isTouch == false) {
            if ($catData->getData('smartmenu_static_bottom') && $catStyle != 'dropdown_simple') {
                $html .= '<div class="mbmenu-block mbmenu-block-level1-top std">';
                $html .= $this->_getStatic($catData->getData('smartmenu_static_bottom'));
                $html .= '</div>';
            }
        }
        $html .= '</li>';

        return $html;
    }

    protected function _getCatLabel($catInfo, $catLevel)
    {
        $catLabel = $catInfo->getData('smartmenu_cat_label');
        if ($catLabel) {
            if ($catLevel == 0) {
                return ' <span class="cat-label cat-label-' . $catLabel . ' pin-bottom">' . __($catLabel) . '</span>';
            } else {
                return ' <span class="cat-label cat-label-' . $catLabel . '">' . __($catLabel) . '</span>';
            }
        }

        return '';
    }

    /**
     * @param $catLevel
     *
     * @return string
     */
    protected function _getCatPosition($catLevel)
    {
        if ($catLevel == 0) {
            $startLevel = isset($this->_catPosLevel[$catLevel]) ? $this->_catPosLevel[$catLevel] + 1 : 1;
            $this->_catPosLevel = [];
            $this->_catPosLevel[$catLevel] = $startLevel;
        } elseif (isset($this->_catPosLevel[$catLevel])) {
            ++$this->_catPosLevel[$catLevel];
        } else {
            $this->_catPosLevel[$catLevel] = 1;
        }

        $position = [];
        for ($i = 0; $i <= $catLevel; ++$i) {
            if (isset($this->_catPosLevel[$i])) {
                $position[] = $this->_catPosLevel[$i];
            }
        }

        return implode('-', $position);
    }
}
