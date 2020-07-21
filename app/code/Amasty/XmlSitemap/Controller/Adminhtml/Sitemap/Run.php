<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Controller\Adminhtml\Sitemap;

use Magento\Backend\App\Action;
use Amasty\XmlSitemap\Api\SitemapRepositoryInterface;

class Run extends Action
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
            if (!$sitemap->getId()) {
                $this->messageManager->addErrorMessage(__('Sitemap does not exist'));
                $this->_redirect('*/*/');
            }

            $sitemap->run();
            $this->messageManager->addSuccessMessage(__('Sitemap has been generated'));
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
