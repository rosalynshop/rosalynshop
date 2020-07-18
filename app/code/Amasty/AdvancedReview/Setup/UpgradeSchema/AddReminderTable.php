<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class AddReminderTable
 * @package Amasty\AdvancedReview\Setup\UpgradeSchema
 */
class AddReminderTable
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('amasty_advanced_review_reminder');
        $orderTableName = $setup->getTable('sales_order');
        $table = $setup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['default' => 0, 'unsigned' => true, 'nullable' => false],
                'Order id'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )
            ->addColumn(
                'send_date',
                Table::TYPE_TIMESTAMP,
                null,
                ['default' => null],
                'Send Date'
            )
            ->addColumn(
                'status',
                Table::TYPE_INTEGER,
                null,
                ['default' => 0, 'nullable' => false],
                'Status'
            )->addIndex(
                $setup->getIdxName(
                    $tableName,
                    [ReminderInterface::ENTITY_ID, 'order_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [ReminderInterface::ENTITY_ID, 'order_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName($tableName, ['order_id']),
                ['order_id']
            )->addForeignKey(
                $setup->getFkName($tableName, 'order_id', $orderTableName, 'entity_id'),
                'order_id',
                $setup->getTable('sales_order'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Advanced review reminder');
        $setup->getConnection()->createTable($table);
    }
}
