<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\ResourceModel;

use Amasty\Fpc\Setup\Operation\CreateFlushPagesTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FlushPages extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(CreateFlushPagesTable::TABLE_NAME, 'id');
    }
}
