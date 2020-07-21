<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


declare(strict_types=1);

namespace Amasty\Fpc\Setup\Operation;

use Amasty\Fpc\Api\Data\FlushesLogInterface;
use Amasty\Fpc\Model\ResourceModel\FlushesLog;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeTo220
{
    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createFlushesLogTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createFlushesLogTable(SchemaSetupInterface $setup): Table
    {
        return $setup->getConnection()
            ->newTable($setup->getTable(FlushesLog::TABLE_NAME))
            ->addColumn(
                FlushesLogInterface::LOG_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                FlushesLogInterface::SOURCE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true]
            )
            ->addColumn(
                FlushesLogInterface::DETAILS,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true]
            )
            ->addColumn(
                FlushesLogInterface::TAGS,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true]
            )
            ->addColumn(
                FlushesLogInterface::SUBJECT,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true]
            )
            ->addColumn(
                FlushesLogInterface::DATE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true]
            )
            ->addColumn(
                FlushesLogInterface::BACKTRACE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true]
            )
            ->setComment('Amasty FPC Flushes Log Table');
    }
}
