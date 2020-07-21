<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Setup;

use Amasty\XmlSitemap\Model\Hreflang\GetCmsPageRelationFieldInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->addDateFormatColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $this->addProductStatusColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.3.0') < 0) {
            $this->addHreflangColumns($setup);
        }

        if (version_compare($context->getVersion(), '1.3.1') < 0) {
            $this->addFaqColumns($setup);
        }

        if (version_compare($context->getVersion(), '1.3.12') < 0) {
            $this->updateExtraLinks($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addDateFormatColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('amasty_xml_sitemap'),
                'date_format',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => false,
                    'comment' => 'Date Format'
                ]
            );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addProductStatusColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('amasty_xml_sitemap'),
                'exclude_out_of_stock',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'comment' => 'Exclude Out Of Stock Products'
                ]
            );
    }

    public function addFaqColumns(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->addColumn(
            $setup->getTable('amasty_xml_sitemap'),
            'faq',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Faq'

            ]
        );

        $connection->addColumn(
            $setup->getTable('amasty_xml_sitemap'),
            'faq_priority',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 4,
                'nullable' => false,
                'default' => 0.5,
                'comment' => 'Faq Priority'

            ]
        );

        $connection->addColumn(
            $setup->getTable('amasty_xml_sitemap'),
            'faq_frequency',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 16,
                'nullable' => false,
                'comment' => 'Faq Frequency'

            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addHreflangColumns(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('amasty_xml_sitemap'),
                'hreflang_product',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Is hreflang used for products'
                ]
            );
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_xml_sitemap'),
            'hreflang_category',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is hreflang used for categories'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_xml_sitemap'),
            'hreflang_cms',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is hreflang used for CMS pages'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            GetCmsPageRelationFieldInterface::FIELD_CMS_UUID,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'default' => null,
                'comment' => 'UUID for Amasty SEO Hreflang'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function updateExtraLinks(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->modifyColumnByDdl(
                $setup->getTable('amasty_xml_sitemap'),
                'extra_links',
                [
                    'DATA_TYPE' => 'mediumtext',
                    'LENGTH' => '2M',
                    'nullable' => false,
                    'comment' => 'Extra Links'
                ]
            );
    }
}
