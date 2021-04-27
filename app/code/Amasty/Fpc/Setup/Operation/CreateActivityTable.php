<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\Fpc\Api\Data\ActivityInterface;

class CreateActivityTable
{
    const TABLE_NAME = 'amasty_fpc_activity';

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
        $table = $setup->getTable(self::TABLE_NAME);

        return $setup->getConnection()
            ->newTable($table)
            ->setComment('Amasty FPC Activity Table')
            ->addColumn(
                ActivityInterface::ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Id'
            )
            ->addColumn(
                ActivityInterface::RATE,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 1
                ],
                'Number of Visits'
            )->addColumn(
                ActivityInterface::URL,
                Table::TYPE_TEXT,
                255,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Url'
            )->addColumn(
                ActivityInterface::STORE,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Store Id'
            )->addColumn(
                ActivityInterface::CURRENCY,
                Table::TYPE_TEXT,
                255,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Currency'
            )->addColumn(
                ActivityInterface::CUSTOMER_GROUP,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Customer Group'
            )->addColumn(
                ActivityInterface::MOBILE,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Mobile'
            )->addColumn(
                ActivityInterface::STATUS,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Status'
            )->addColumn(
                ActivityInterface::DATE,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Date'
            );
    }
}
