<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Controller\Adminhtml\Sitemap;

use Magento\Backend\App\Action;
use Amasty\XmlSitemap\Api\SitemapRepositoryInterface;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var SitemapRepositoryInterface $sitemapRepository
     */
    private $sitemapRepository;

    public function __construct(
        Action\Context $context,
        SitemapRepositoryInterface $sitemapRepository
    ) {
        parent::__construct($context);
        $this->sitemapRepository = $sitemapRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        try {
            $sitemap = $this->sitemapRepository->getById($id);

            if ($id && !$sitemap->getId()) {
                $this->messageManager->addErrorMessage(__('Record does not exist'));
                $this->_redirect('*/*/');
                return;
            }

            $this->sitemapRepository->delete($sitemap);
            $this->messageManager->addSuccessMessage(
                __('Item has been successfully deleted')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/');
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
