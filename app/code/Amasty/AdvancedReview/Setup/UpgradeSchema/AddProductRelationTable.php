<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Amasty\AdvancedReview\Model\ResourceModel\ReminderProduct;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddProductRelationTable
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();

        $reminderProductTable = $setup->getTable(ReminderProduct::MAIN_TABLE);
        $productTable = $setup->getTable('catalog_product_entity');
        $customerTable = $setup->getTable('customer_entity');

        $table = $connection->newTable($reminderProductTable)->addColumn(
            ReminderProduct::CUSTOMER_EMAIL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Email'
        )->addColumn(
            ReminderProduct::PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product Id'
        )->addIndex(
            $setup->getIdxName(
                $reminderProductTable,
                [ReminderProduct::CUSTOMER_EMAIL, ReminderProduct::PRODUCT_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [ReminderProduct::CUSTOMER_EMAIL, ReminderProduct::PRODUCT_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $setup->getFkName($reminderProductTable, ReminderProduct::PRODUCT_ID, $productTable, 'entity_id'),
            ReminderProduct::PRODUCT_ID,
            $productTable,
            'entity_id',
            Table::ACTION_CASCADE
        );
        $connection->createTable($table);
    }
}
