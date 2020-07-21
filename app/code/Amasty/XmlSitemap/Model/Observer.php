<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model;

use Amasty\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory;

class Observer
{
    /** @var ResourceModel\Sitemap\CollectionFactory $_sitemapCollection */
    private $itemapCollection;

    /**
     * Observer constructor.
     * @param ResourceModel\Sitemap\CollectionFactory $sitemapCollection
     */
    public function __construct(
        CollectionFactory $sitemapCollection
    ) {
        $this->itemapCollection = $sitemapCollection;
    }

    public function process()
    {
        /** @var ResourceModel\Sitemap\Collection $profiles */
        $profiles = $this->itemapCollection->create();

        /** @var Sitemap $profile */
        foreach ($profiles as $profile) {
            $profile->generateXml();
        }
    }
}
