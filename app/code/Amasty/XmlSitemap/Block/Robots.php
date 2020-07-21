<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Magento\Robots\Model\Config\Value;
use Amasty\XmlSitemap\Helper\Data as SitemapHelper;
use Amasty\XmlSitemap\Model\ResourceModel\Sitemap\Collection;
use Magento\Store\Model\StoreManagerInterface;

class Robots extends AbstractBlock implements IdentityInterface
{
    /**
     * @var SitemapHelper
     */
    private $sitemapHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Collection
     */
    private $sitemapCollection;

    public function __construct(
        Context $context,
        Collection $sitemapCollection,
        SitemapHelper $sitemapHelper,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->sitemapHelper = $sitemapHelper;
        $this->storeManager = $storeManager;
        $this->sitemapCollection = $sitemapCollection;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $defaultStore = $this->storeManager->getDefaultStoreView();

        /** @var \Magento\Store\Model\Website $website */
        $website = $this->storeManager->getWebsite($defaultStore->getWebsiteId());

        $storeIds = [];
        foreach ($website->getStoreIds() as $storeId) {
            if ((bool)$this->sitemapHelper->getEnableSubmissionRobots($storeId)) {
                $storeIds[] = (int)$storeId;
            }
        }

        $links = [];
        if ($storeIds) {
            $links = $this->getSitemapLinks($storeIds);
        }

        return $links ? implode(PHP_EOL, $links) . PHP_EOL : '';
    }

    /**
     * @param array $storeIds
     * @return array
     */
    protected function getSitemapLinks(array $storeIds)
    {
        $sitemapLinks = [];
        $this->sitemapCollection->addStoreFilter($storeIds);

        foreach ($this->sitemapCollection as $sitemap) {
            $sitemapUrl = $this->sitemapHelper->getCorrectUrl($sitemap->getFolderName(), 0);
            $sitemapLinks[$sitemapUrl] = __('Sitemap: ') . $sitemapUrl;
        }

        return $sitemapLinks;
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [
            Value::CACHE_TAG . '_' . $this->storeManager->getDefaultStoreView()->getId(),
        ];
    }
}
