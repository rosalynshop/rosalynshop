<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateLogTable
{
    const TABLE_NAME = 'amasty_fpc_log';

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createTable(SchemaSetupInterface $setup)
    {
        $describe = $setup->getConnection()->describeTable($setup->getTable('customer_group'));

        return $setup->getConnection()
            ->newTable($setup->getTable('amasty_fpc_log'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
            )
            ->addColumn(
                'url',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'store',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true]
            )
            ->addColumn(
                'currency',
                Table::TYPE_TEXT,
                3,
                ['nullable' => true]
            )
            ->addColumn(
                'customer_group',
                $describe['customer_group_id']['DATA_TYPE'] == 'int' ? Table::TYPE_INTEGER : Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true]
            )
            ->addColumn(
                'rate',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'load_time',
                Table::TYPE_FLOAT,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addForeignKey(
                $setup->getFkName(
                    'amasty_fpc_log',
                    'store',
                    'store',
                    'store_id'
                ),
                'store',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    'amasty_fpc_log',
                    'customer_group',
                    'customer_group',
                    'customer_group_id'
                ),
                'customer_group',
                $setup->getTable('customer_group'),
                'customer_group_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty FPC Log Table');
    }
}
