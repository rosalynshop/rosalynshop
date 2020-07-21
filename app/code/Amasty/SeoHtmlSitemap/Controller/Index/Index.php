<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoHtmlSitemap
 */


namespace Amasty\SeoHtmlSitemap\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Amasty\SeoHtmlSitemap\Helper\Data as SeoSitemapHelper;

class Index extends \Magento\Framework\App\Action\Action
{
    private $_helper;

    protected $_resultPageFactory;
    
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        SeoSitemapHelper $helper
    ) {
        $this->_helper = $helper;
        $this->_resultPageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();

        $layout = $this->_helper->getLayout();
        if ($layout) {
            $resultPage->getConfig()->setPageLayout($layout);
        }

        $pageTitle  = $this->_helper->getPageTitle();
        if ($pageTitle) {
            $resultPage->getConfig()->getTitle()->set($pageTitle);
        }

        $pageMetaDescription = $this->_helper->getMetaDescription();
        if ($pageMetaDescription) {
            $resultPage->getConfig()->setDescription($pageMetaDescription);
        }

        return $resultPage;
    }
}