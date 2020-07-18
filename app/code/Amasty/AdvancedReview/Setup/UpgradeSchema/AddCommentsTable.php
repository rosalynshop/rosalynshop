<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddCommentsTable
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(CommentInterface::TABLE))
            ->addColumn(
                CommentInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Comment Id'
            )->addColumn(
                CommentInterface::REVIEW_ID,
                Table::TYPE_BIGINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Review Id'
            )->addColumn(
                CommentInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => false],
                'Store Id'
            )->addColumn(
                CommentInterface::STATUS,
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false, 'primary' => false],
                'Status'
            )->addColumn(
                CommentInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'primary' => false, 'default' => null],
                'Customer Id'
            )->addColumn(
                CommentInterface::MESSAGE,
                Table::TYPE_TEXT,
                null,
                [],
                'Message'
            )->addColumn(
                CommentInterface::NICKNAME,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Nickname'
            )->addColumn(
                CommentInterface::EMAIL,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Email'
            )->addColumn(
                CommentInterface::SESSION_ID,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Session Id'
            )->addColumn(
                CommentInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                CommentInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addForeignKey(
                $setup->getFkName(
                    CommentInterface::TABLE,
                    CommentInterface::REVIEW_ID,
                    'review',
                    'review_id'
                ),
                CommentInterface::REVIEW_ID,
                $setup->getTable('review'),
                'review_id',
                Table::ACTION_CASCADE
            );

        $setup->getConnection()->createTable($table);
    }
}
