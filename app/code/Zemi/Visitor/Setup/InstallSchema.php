<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\Visitor\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup;

/**
 * Class InstallSchema
 * @package Zemi\RegionManager\Setup
 */
class InstallSchema implements Setup\InstallSchemaInterface
{
    /**
     * @param Setup\SchemaSetupInterface $setup
     * @param Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(Setup\SchemaSetupInterface $setup, Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('zm_visitor_customer')
        )
            ->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'auto_increment' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )
            ->addColumn(
            'ip_address',
            Table::TYPE_TEXT,
            null,
            [],
            'Ip Address'
        )
            ->addColumn(
                'product_id',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Product Id'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['default' => null],
                'Customer Id'
            )
            ->addColumn(
                'date_time',
                Table::TYPE_DATETIME,
                null,
                ['default' => null],
                'Date time'
            )
            ->setComment(
            'Customer Visitor'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
