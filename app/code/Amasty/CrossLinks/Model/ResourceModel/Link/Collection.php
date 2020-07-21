<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model\ResourceModel\Link;

use Amasty\CrossLinks\Model\Link;

/**
 * Class Collection
 * @package Amasty\CrossLinks\Model\ResourceModel\Link
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'link_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Amasty\CrossLinks\Model\Link::class,
            \Amasty\CrossLinks\Model\ResourceModel\Link::class
        );
    }

    /**
     * @param array $storeIds
     * @return $this
     */
    public function addStoreIdFilter(array $storeIds)
    {
        $this->getSelect()
            ->joinInner(
                ['link_store' => $this->getTable('amasty_cross_link_store')],
                'main_table.link_id = link_store.link_id',
                []
            )->where('link_store.store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function addStatusFilter($status = Link::STATUS_ACTIVE)
    {
        $this->getSelect()->where('main_table.status = ?', $status);
        return $this;
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('amasty_cross_link_store'), 'link_id')
            ->columns(['store_ids' => new \Zend_Db_Expr('GROUP_CONCAT(store_id SEPARATOR ",")')])
            ->group('link_id');
        $storeData = $this->getConnection()->fetchAll($select);
        foreach ($storeData as $data) {
            if ($item = $this->getItemById($data['link_id'])) {
                $item->setStoreIds(explode(',', $data['store_ids']));
            }

        }
        return parent::_afterLoad();
    }

    /**
     * @return $this
     */
    public function addPriorityOrder()
    {
        $this->getSelect()->order('main_table.priority ASC');
        return $this;
    }

    /**
     * @return $this
     */
    public function groupById()
    {
        $this->getSelect()->group('main_table.link_id');
        return $this;
    }
}
