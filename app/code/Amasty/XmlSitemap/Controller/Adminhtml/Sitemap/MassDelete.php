<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Controller\Adminhtml\Sitemap;

use Amasty\XmlSitemap\Api\SitemapInterface;

class MassDelete extends \Amasty\XmlSitemap\Controller\Adminhtml\AbstractMassAction
{
    /**
     * @param SitemapInterface $siteMap
     */
    protected function itemAction(SitemapInterface $siteMap)
    {
        $this->sitemapRepository->delete($siteMap);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deleted.', $collectionSize);
        }

        return __('No records have been deleted.');
    }
}