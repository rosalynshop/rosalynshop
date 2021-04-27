<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\ResourceModel\Activity;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\DB\Select;
use Amasty\Fpc\Model\Activity;
use Amasty\Fpc\Model\ResourceModel\Activity as ActivityResource;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Activity::class, ActivityResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Get data from activity table
     *
     * @param $queueLimit
     *
     * @return array
     */
    public function getPagesData($queueLimit)
    {
        $this->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(['url', 'rate', 'store'])
            ->where('status NOT IN (?)', [404])
            ->limit($queueLimit);

        return $this->getData();
    }
}
