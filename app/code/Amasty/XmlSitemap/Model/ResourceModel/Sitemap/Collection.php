<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\ResourceModel\Sitemap;

use Magento\Store\Model\Store;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\XmlSitemap\Model\Sitemap::class,
            \Amasty\XmlSitemap\Model\ResourceModel\Sitemap::class
        );
        $this->_idFieldName = 'id';
    }

    /**
     * @param $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->addFieldToFilter(Store::STORE_ID, $storeIds);
        return $this;
    }
}
