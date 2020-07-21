<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Controller\Adminhtml;

use Amasty\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Amasty\XmlSitemap\Model\ResourceModel\SitemapFactory;
use Amasty\XmlSitemap\Api\SitemapInterface;

abstract class AbstractMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_XmlSitemap::sitemap';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SitemapFactory
     */
    protected $sitemapFactory;

    /**
     * @var \Amasty\XmlSitemap\Model\Repository\SitemapRepository
     */
    protected $sitemapRepository;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        SitemapFactory $sitemapFactory,
        \Amasty\XmlSitemap\Model\Repository\SitemapRepository $sitemapRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->sitemapFactory = $sitemapFactory;
        $this->sitemapRepository = $sitemapRepository;
    }

    /**
     * Execute action for sitemap
     *
     * @param SitemapInterface $siteMap
     */
    abstract protected function itemAction(SitemapInterface $siteMap);

    /**
     * Mass action execution
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider(); // compatibility with Mass Actions on Magento 2.1.0
        /** @var \Amasty\XmlSitemap\Model\ResourceModel\Sitemap\Collection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                foreach ($collection->getItems() as $sitemap) {
                    $this->itemAction($sitemap);
                }

                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been changed.', $collectionSize);
        }

        return __('No records have been changed.');
    }
}
