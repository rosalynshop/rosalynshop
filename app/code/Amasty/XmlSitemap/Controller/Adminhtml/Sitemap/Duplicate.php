<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Controller\Adminhtml\Sitemap;

use Magento\Backend\App\Action;
use Amasty\XmlSitemap\Api\SitemapRepositoryInterface;

class Duplicate extends \Magento\Backend\App\Action
{
    /**
     * @var SitemapRepositoryInterface $sitemapRepository
     */
    private $sitemapRepository;

    /**
     * Duplicate constructor.
     * @param Action\Context $context
     * @param SitemapRepositoryInterface $sitemapRepository
     */
    public function __construct(
        Action\Context $context,
        SitemapRepositoryInterface $sitemapRepository
    ) {
        parent::__construct($context);
        $this->sitemapRepository = $sitemapRepository;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        try {
            /** @var \Amasty\XmlSitemap\Model\Sitemap $model */
            $model = $this->sitemapRepository->getById($id);
            $data = $model->getData();
            unset($data['id']);
            $model->unsetData('id');
            $this->sitemapRepository->save($model);

            $this->messageManager->addSuccessMessage(__('Sitemap was successfully duplicated'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/index');
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
