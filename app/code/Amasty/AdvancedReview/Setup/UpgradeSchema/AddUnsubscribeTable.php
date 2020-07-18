<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class AddUnsubscribeTable
 * @package Amasty\AdvancedReview\Setup\UpgradeSchema
 */
class AddUnsubscribeTable
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('amasty_advanced_review_unsubscribe');
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
                'unsubscribed_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Unsubscribed At'
            )
            ->addColumn(
                'email',
                Table::TYPE_TEXT,
                256,
                ['nullable' => false],
                'Email'
            )
            ->setComment('Advanced review reminder unsubscribe');
        $setup->getConnection()->createTable($table);
    }
}
