<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package Amasty\AdvancedReview\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createModuleTables($setup);
        $this->addReviewColumns($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createModuleTables(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_advanced_review_images'))
            ->addColumn(
                'image_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Review Id'
            )
            ->addColumn(
                'review_id',
                Table::TYPE_INTEGER,
                null,
                ['default' => 0, 'nullable' => false],
                'Review table id'
            )
            ->addColumn(
                'path',
                Table::TYPE_TEXT,
                '2M',
                [],
                'Image path'
            )
            ->setComment('Advanced review images table');
        $installer->getConnection()->createTable($table);

        $tableVote = $installer->getConnection()
            ->newTable($installer->getTable('amasty_advanced_review_vote'))
            ->addColumn(
                'vote_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Vote Id'
            )
            ->addColumn(
                'review_id',
                Table::TYPE_INTEGER,
                null,
                ['default' => 0, 'nullable' => false],
                'Review table id'
            )
            ->addColumn(
                'type',
                Table::TYPE_SMALLINT,
                null,
                [],
                'type'
            )
            ->addColumn(
                'ip',
                Table::TYPE_TEXT,
                256,
                [],
                'ip'
            )
            ->setComment('Advanced review vote table');
        $installer->getConnection()->createTable($tableVote);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function addReviewColumns(SchemaSetupInterface $installer)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable('review'),
            'answer',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'comment' => 'Admin answer'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('review'),
            'verified_buyer',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Verified Buyer'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('review'),
            'is_recommended',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Is Recommended'
            ]
        );
    }
}
