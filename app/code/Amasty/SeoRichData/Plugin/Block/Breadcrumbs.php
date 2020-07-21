<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Plugin\Block;

use Amasty\SeoRichData\Model\DataCollector;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ViewInterface;
use Amasty\SeoRichData\Helper\Config as ConfigHelper;
use Magento\Catalog\Block\Breadcrumbs as CatalogBreadcrumbs;
use Magento\Theme\Block\Html\Breadcrumbs as HtmlBreadcrubms;
use Magento\Framework\App\RequestInterface;


class Breadcrumbs
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $view;

    /**
     * @var DataCollector
     */
    protected $dataCollector;

    /**
     * @var \Amasty\SeoRichData\Helper\Config
     */
    private $configHelper;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ViewInterface $view,
        DataCollector $dataCollector,
        ConfigHelper $configHelper,
        RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->view = $view;
        $this->dataCollector = $dataCollector;
        $this->configHelper = $configHelper;
        $this->request = $request;
    }

    /**
     * @param HtmlBreadcrubms $subject
     * @param $key
     * @param $value
     */
    public function beforeAssign(
        HtmlBreadcrubms $subject,
        $key,
        $value
    ) {
        if ($key == 'crumbs' && $this->configHelper->isBreadcrumbsEnabled()) {
            $this->dataCollector->setData('breadcrumbs', $value);
        }
    }

    /**
     * @param HtmlBreadcrubms $subject
     */
    public function beforeToHtml(HtmlBreadcrubms $subject)
    {
        if (!$subject->getLayout()->getBlock('breadcrumbs_0')
            && ($this->isCategoryPage() || $this->isProductViewPage())
        ) {
            $subject->getLayout()->createBlock(CatalogBreadcrumbs::class);
        }
    }

    /**
     * @return bool
     */
    private function isProductViewPage()
    {
        return $this->request->getModuleName() == 'catalog'
            && $this->request->getControllerName() == 'product';
    }

    /**
     * @return bool
     */
    private function isCategoryPage()
    {
        return $this->request->getModuleName() == 'catalog'
            && $this->request->getControllerName() == 'category';
    }
}
