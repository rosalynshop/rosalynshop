<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Controller\Adminhtml\Sitemap;

use Magento\Backend\App\Action;
use Amasty\XmlSitemap\Api\SitemapRepositoryInterface;
use Amasty\XmlSitemap\Model\SitemapFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var SitemapRepositoryInterface $sitemapRepository
     */
    private $sitemapRepository;

    /**
     * @var \Magento\Framework\Registry $_coreRegistry
     */
    private $coreRegistry;

    /**
     * @var SitemapFactory
     */
    private $sitemapFactory;

    public function __construct(
        Action\Context $context,
        SitemapRepositoryInterface $sitemapRepository,
        \Magento\Framework\Registry $coreRegistry,
        SitemapFactory $sitemapFactory
    ) {
        parent::__construct($context);
        $this->sitemapRepository = $sitemapRepository;
        $this->coreRegistry = $coreRegistry;
        $this->sitemapFactory = $sitemapFactory;
    }

    public function execute()
    {
        $model = $this->sitemapFactory->create();
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                /** @var \Amasty\XmlSitemap\Model\Sitemap $model */
                $model = $this->sitemapRepository->getById($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This sitemap no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        if ($model->getId() || $id == 0) {
            $data = $this->_session->getPageData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            $this->coreRegistry->register('amxmlsitemap_profile', $model);

            $this->_view->loadLayout();
            $this->_setActiveMenu('Amasty_XmlSitemap::xml_sitemap')
                ->_addBreadcrumb(__('XML Google Sitemap'), __('XML Google Sitemap'));

            if ($model->getId()) {
                $title = __('Edit Item `%1`', $model->getTitle());
            } else {
                $title = __("Add Item");
            }
            $this->_view->getPage()->getConfig()->getTitle()->prepend($title);

            $this->_view->renderLayout();
        } else {
            $this->messageManager->addErrorMessage(__('Item does not exist'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
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
