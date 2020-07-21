<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Model\ResourceModel\Config;

class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init('Amasty\Meta\Model\Config', 'Amasty\Meta\Model\ResourceModel\Config');
    }

    /**
     * @return $this
     */
    public function addCategoryFilter()
    {
        return $this->_addFilterByCustomField(false);
    }

    /**
     * @return $this
     */
    public function addCustomFilter()
    {
        return $this->_addFilterByCustomField(true);
    }

    protected function _addFilterByCustomField($value)
    {
        $this->getSelect()
            ->where('is_custom = ?' , $value);

        return $this;
    }

    /**
     * @param $urls
     * @param null $storeId
     *
     * @return $this
     */
    public function addUrlFilter($urls, $storeId = null)
    {
        foreach ($urls as &$url) {
            $url = trim($url, '/');
        }

        $this->addCustomFilter();

        $select = $this->getSelect();

        $where = [];
        foreach ($urls as $itemUrl) {
            $itemUrl = $this->getConnection()->quote($itemUrl);
            $where[] = $itemUrl . ' LIKE REPLACE(TRIM("/" FROM custom_url), "*", "%")';
        }

        // Trick to avoid quoteInto call and preserve ? character
        $wherePart = $select->getPart(\Magento\Framework\DB\Select::WHERE);
        $wherePart[] = 'AND ('.implode(' OR ', $where).')';
        $select->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);

        if ($storeId) {
            $select->where('store_id IN (?)', array((int) $storeId, 0));
        }

        return $this;
    }
}