<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

interface GetUrlsInterface
{
    /**
     * @param array $storeIds
     * @param array|null $ids
     * @return array
     */
    public function execute($storeIds, array $ids = null);
}
