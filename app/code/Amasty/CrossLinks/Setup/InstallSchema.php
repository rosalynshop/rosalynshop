<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */

namespace Amasty\CrossLinks\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package Amasty\CrossLinks\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $context->getVersion();
        $tableName = $installer->getTable('amasty_cross_link');
        $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'link_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn('status', Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0])
            ->addColumn('title', Table::TYPE_TEXT, 80, ['nullable' => false])
            ->addColumn('keywords', Table::TYPE_TEXT, null, ['nullable' => false])
            ->addColumn('link_target', Table::TYPE_TEXT, 10, ['nullable' => false])
            ->addColumn('reference_type', Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0])
            ->addColumn('reference_resource', Table::TYPE_TEXT, 100, ['nullable' => false])
            ->addColumn('replacement_limit', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => 0])
            ->addColumn('priority', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => 0])
            ->addColumn('is_nofollow', Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0]);

        $installer->getConnection()->createTable($table);

        $tableName = $installer->getTable('amasty_cross_link_store');
        $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn('link_id', Table::TYPE_INTEGER, null, ['nullable' => false])
            ->addColumn('store_id', Table::TYPE_SMALLINT, 1, ['nullable' => false])
            ->addIndex(
                $installer->getIdxName(
                    'amasty_cross_link_store',
                    ['link_id', 'store_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['link_id', 'store_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
