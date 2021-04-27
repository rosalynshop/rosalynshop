<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DB\Select;
use Amasty\Fpc\Setup\Operation\CreateActivityTable;

class Activity extends AbstractDb
{
    public function _construct()
    {
        $this->_init(CreateActivityTable::TABLE_NAME, 'id');
    }

    public function truncate()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    /**
     * Get activity by url and version (mobile or not)
     *
     * @param string $url
     * @param bool $mobile
     *
     * @return bool
     */
    public function matchUrl($url, $mobile)
    {
        $select = $this->getConnection()->select()
            ->from(['activity' => $this->getMainTable()])
            ->where('activity.url = ?', $url)
            ->where('activity.mobile = ?', $mobile)
            ->reset(Select::COLUMNS)
            ->columns('activity.id');

        return (int)$this->getConnection()->fetchOne($select);
    }
}
