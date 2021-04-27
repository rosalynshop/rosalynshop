<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Amasty\Fpc\Api\Data\ActivityInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\CreateActivityTable
     */
    private $createActivityTable;

    /**
     * @var Operation\CreateFlushPagesTable
     */
    private $createFlushPagesTable;

    /**
     * @var Operation\CreateReportsTable
     */
    private $createReportsTable;

    /**
     * @var Operation\UpgradeTo220
     */
    private $upgradeTo220;

    public function __construct(
        Operation\CreateFlushPagesTable $createFlushPagesTable,
        Operation\CreateActivityTable $createActivityTable,
        Operation\CreateReportsTable $createReportsTable,
        Operation\UpgradeTo220 $upgradeTo220
    ) {
        $this->createFlushPagesTable = $createFlushPagesTable;
        $this->createActivityTable = $createActivityTable;
        $this->createReportsTable = $createReportsTable;
        $this->upgradeTo220 = $upgradeTo220;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->createActivityTable->execute($setup);
        }

        if (!$context->getVersion() || version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->createFlushPagesTable->execute($setup);
        }

        if (!$context->getVersion() || version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->createReportsTable->execute($setup);
            $this->addMobileColumn($setup);
        }

        if (!$context->getVersion() || version_compare($context->getVersion(), '2.1.2', '<')) {
            $this->addActivityIndexes($setup);
        }

        if (!$context->getVersion() || version_compare($context->getVersion(), '2.2.0', '<')) {
            $this->upgradeTo220->execute($setup);
        }

        $setup->endSetup();
    }

    private function addMobileColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(Operation\CreateLogTable::TABLE_NAME),
            'mobile',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Mobile'
        );
    }

    private function addActivityIndexes(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addIndex(
            $setup->getTable(Operation\CreateActivityTable::TABLE_NAME),
            $setup->getIdxName(
                $setup->getTable(Operation\CreateActivityTable::TABLE_NAME),
                [ActivityInterface::URL, ActivityInterface::MOBILE],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [ActivityInterface::URL, ActivityInterface::MOBILE],
            AdapterInterface::INDEX_TYPE_INDEX
        );
    }
}
