<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateQueueTable
{
    const TABLE_NAME = 'amasty_fpc_queue_page';

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
        return $setup->getConnection()
            ->newTable($setup->getTable(self::TABLE_NAME))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'url',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'rate',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'store',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true]
            )
            ->addForeignKey(
                $setup->getFkName(
                    'amasty_fpc_queue_page',
                    'store',
                    'store',
                    'store_id'
                ),
                'store',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->addIndex(
                $setup->getIdxName('amasty_fpc_queue', ['rate']),
                ['rate']
            )
            ->setComment('Amasty FPC Queue Table');
    }
}
