<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateReportsTable
{
    const TABLE_NAME = 'amasty_fpc_reports';

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
                'date',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default'  => Table::TIMESTAMP_INIT
                ],
                'Visit Time'
            )->addColumn(
                'status',
                Table::TYPE_TEXT,
                25,
                [
                    'nullable' => false,
                ],
                'Page status'
            )->addColumn(
                'response',
                Table::TYPE_FLOAT,
                25,
                [
                    'nullable' => false
                ],
                'Page response time'
            );
    }
}