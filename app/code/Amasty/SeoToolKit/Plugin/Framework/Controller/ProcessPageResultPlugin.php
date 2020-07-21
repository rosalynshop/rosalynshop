<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Plugin\Framework\Controller;

use Amasty\SeoToolKit\Helper\Config;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;

/**
 * Find last page only after rendering product list block. In other case - fatal with elasticsearch
 */
class ProcessPageResultPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    public function __construct(
        Config $config,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->config = $config;
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
        $this->layout = $layout;
    }

    /**
     * @param ResultInterface $subject
     * @param string $result
     * @param ResponseInterface $response
     *
     * @return string
     */
    public function afterRenderResult(ResultInterface $subject, $result, ResponseInterface $response)
    {
        if ($this->config->isPrevNextLinkEnabled()) {
            $output = $response->getBody();
            $output = $this->modifyBody($output);
            $response->setBody($output);
        }

        return $result;
    }

    /**
     * @param string $output
     * @return string $output
     */
    public function modifyBody($output)
    {
        $html = $this->getPrevNextLinkContent();
        if ($html) {
            $head = '</head>';
            $output = str_replace($head, $html . $head, $output);
        }

        return $output;
    }

    /**
     * @return string $html
     */
    public function getPrevNextLinkContent()
    {
        $html = '';
        $productListBlock = $this->getCategoryProductListBlock();
        if ($productListBlock) {
            $toolbarBlock = $productListBlock->getToolbarBlock();
            /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
            $pagerBlock = $toolbarBlock->getChildBlock('product_list_toolbar_pager');
            if ($pagerBlock) {
                $pagerBlock
                    ->setLimit($toolbarBlock->getLimit())
                    ->setAvailableLimit($toolbarBlock->getAvailableLimit())
                    ->setCollection($productListBlock->getLayer()->getProductCollection());
                $lastPage = $pagerBlock->getLastPageNum();
                $currentPage = $pagerBlock->getCurrentPage();

                if ($currentPage > 1) {
                    $url = $this->getPageUrl($pagerBlock->getPageVarName(), $currentPage - 1);
                    $html .= sprintf($this->getLinkTemplate(), 'prev', $url);
                }

                if ($currentPage < $lastPage) {
                    $url = $this->getPageUrl($pagerBlock->getPageVarName(), $currentPage + 1);
                    $html .= sprintf($this->getLinkTemplate(), 'next', $url);
                }
            }
        }

        return $html;
    }

    /**
     * @return \Magento\Catalog\Block\Product\ListProduct
     */
    private function getCategoryProductListBlock()
    {
        $productListBlock = $this->layout->getBlock('category.products.list');
        if (!$productListBlock) {
            foreach ($this->layout->getAllBlocks() as $block) {
                if ($block instanceof \Magento\Catalog\Block\Product\ListProduct) {
                    $productListBlock = $block;
                    break;
                }
            }
        }

        return $productListBlock;
    }

    /**
     * @param string $key
     * @param int value
     * @return string
     */
    private function getPageUrl($key, $value)
    {
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        $currentUrl = $this->escaper->escapeUrl($currentUrl);
        $result = preg_replace('/(\W)' . $key . '=\d+/', "$1$key=$value", $currentUrl, -1, $count);
        if ($value == 1) {
            $result = str_replace($key . '=1&amp;', '', $result); //not last & not single param
            $result = str_replace('&amp;' . $key . '=1', '', $result); //last param
            $result = str_replace('?' . $key . '=1', '', $result); //single param
        } elseif (!$count) {
            $delimiter = (strpos($currentUrl, '?') === false) ? '?' : '&amp;';
            $result .= $delimiter . $key . '=' . $value;
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getLinkTemplate()
    {
        return '<link rel="%s" href="%s" />' . PHP_EOL;
    }
}
