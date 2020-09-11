<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */


namespace Amasty\Smtp\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractDb
{
    const SUBJECT = 'subject';
    const RECIPIENT_EMAIL = 'recipient_email';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_amsmtp_log', 'id');
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function truncate()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    /**
     * @param int $days
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clear($days)
    {
        $days = (int)$days;
        $connection = $this->getConnection();
        $expr = new \Zend_Db_Expr("DATEDIFF(NOW(), created_at) > $days");
        $connection->delete($this->getMainTable(), $expr);
    }
}
