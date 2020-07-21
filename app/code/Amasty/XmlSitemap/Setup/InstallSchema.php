<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('amasty_xml_sitemap'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Title'
            )
            ->addColumn(
                'folder_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Title'
            )
            ->addColumn(
                'max_items',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Max Items'
            )
            ->addColumn(
                'max_file_size',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Max File Size'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                3,
                ['nullable' => false, 'default' => 0, 'unsigned' => true],
                'Type'
            )
            ->addColumn(
                'last_run',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false, 'default' => '0000-00-00 00:00:00'],
                'Last Run'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Store ID'
            )
            ->addColumn(
                'categories',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 1],
                'Categories'
            )
            ->addColumn(
                'categories_modified',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Categories Modified'
            )
            ->addColumn(
                'categories_thumbs',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Categories Thumbs'
            )
            ->addColumn(
                'categories_captions',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Categories Captions'
            )
            ->addColumn(
                'categories_priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => 0.5],
                'Categories Priority'
            )
            ->addColumn(
                'categories_frequency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Categories Frequency'
            )
            ->addColumn(
                'pages',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Pages'
            )
            ->addColumn(
                'pages_priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => 0.5],
                'Pages Priority'
            )
            ->addColumn(
                'pages_frequency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Pages Frequency'
            )
            ->addColumn(
                'pages_modified',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Pages Modified'
            )
            ->addColumn(
                'exclude_cms_aliases',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Exclude CMS aliases'
            )
            ->addColumn(
                'extra',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Extra'
            )
            ->addColumn(
                'extra_priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => 0.5],
                'Extra Priority'
            )
            ->addColumn(
                'extra_frequency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Extra Frequency'
            )
            ->addColumn(
                'extra_links',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Extra Links'
            )
            ->addColumn(
                'products',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Products'
            )
            ->addColumn(
                'products_thumbs',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Products Thumbs'
            )
            ->addColumn(
                'products_captions',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Products Captions'
            )
            ->addColumn(
                'products_captions_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1024,
                ['nullable' => false],
                'Products Captions Template'
            )
            ->addColumn(
                'products_priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => 0.5],
                'Products Priority'
            )
            ->addColumn(
                'products_frequency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Products Frequency'
            )
            ->addColumn(
                'products_modified',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Products Modified'
            )
            ->addColumn(
                'products_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Products URL'
            )
            ->addColumn(
                'landing',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Landing'
            )
            ->addColumn(
                'landing_priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => 0.5],
                'Landing Priority'
            )
            ->addColumn(
                'landing_frequency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Landing Frequency'
            )
            ->addColumn(
                'brands',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Brands'
            )
            ->addColumn(
                'brands_priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => 0.5],
                'Brands Priority'
            )
            ->addColumn(
                'brands_frequency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Brands Frequency'
            )
            ->addColumn(
                'exclude_urls',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Exclude URLs'
            )
            ->addColumn(
                'blog',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Blog'
            )
            ->addColumn(
                'blog_priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => 0.5],
                'Blog Priority'
            )
            ->addColumn(
                'blog_frequency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Blog Frequency'
            )
            ->addColumn(
                'navigation',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Navigation'
            )
            ->addColumn(
                'navigation_priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => 0.5],
                'Navigation Priority'
            )
            ->addColumn(
                'navigation_frequency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Navigation Frequency'
            );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
