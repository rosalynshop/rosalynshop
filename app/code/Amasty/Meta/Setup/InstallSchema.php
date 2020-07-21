<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer
            ->getConnection()
            ->newTable($installer->getTable('amasty_meta_config'))
            ->addColumn(
                'config_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'stores',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )
            ->addColumn(
                'is_custom',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )
            ->addColumn(
                'custom_url',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )


            ->addColumn(
                'custom_meta_title',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'custom_meta_keywords',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'custom_meta_description',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'custom_canonical_url',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'custom_robots',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )
            ->addColumn(
                'custom_h1_tag',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'custom_in_page_text',
                Table::TYPE_TEXT,
                null,
                []
            )


            ->addColumn(
                'cat_meta_title',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'cat_meta_description',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'cat_meta_keywords',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'cat_h1_tag',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'cat_description',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'cat_image_alt',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'cat_image_title',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'cat_after_product_text',
                Table::TYPE_TEXT,
                null,
                []
            )

            ->addColumn(
                'product_meta_title',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'product_meta_keywords',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'product_meta_description',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'product_h1_tag',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'product_short_description',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'product_description',
                Table::TYPE_TEXT,
                null,
                []
            )

            ->addColumn(
                'sub_product_meta_title',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'sub_product_meta_keywords',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'sub_product_meta_description',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'sub_product_h1_tag',
                Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'sub_product_short_description',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addColumn(
                'sub_product_description',
                Table::TYPE_TEXT,
                null,
                []
            )
            ->addIndex('config_id', 'config_id')
        ;

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
