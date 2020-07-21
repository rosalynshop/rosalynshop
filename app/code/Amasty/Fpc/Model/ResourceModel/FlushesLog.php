<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


declare(strict_types=1);

namespace Amasty\Fpc\Model\ResourceModel;

use Amasty\Fpc\Api\Data\FlushesLogInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FlushesLog extends AbstractDb
{
    const TABLE_NAME = 'amasty_fpc_flushes_log';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, FlushesLogInterface::LOG_ID);
    }

    public function truncateTable()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }
}
