<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Controller\Adminhtml\Sitemap;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $pageResult = $this->resultPageFactory->create();
        $pageResult->setActiveMenu('Amasty_XmlSitemap::xml_sitemap');
        $pageResult->addBreadcrumb(__('Manage Google XML Sitemaps'), __('Manage Google XML Sitemaps'));
        $pageResult->getConfig()->getTitle()->prepend(__('Manage Google XML Sitemaps '));

        return $pageResult;
    }

    /**
     * Check if Amasty XML Sitemap is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_XmlSitemap::sitemap');
    }
}
