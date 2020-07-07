<?php

/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package RosalynShop\RegionManager\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            if (!$installer->tableExists('regionmanager_wards')) {
                $table = $installer->getConnection()->newTable($installer->getTable('regionmanager_wards'))
                    ->addColumn('entity_id', Table::TYPE_INTEGER, null,
                        ['identity' => true, 'unsigned' => true, 'auto_increment' => true, 'nullable' => false, 'primary' => true], 'Wards Address')
                    ->addColumn('states_name', Table::TYPE_TEXT, '256', [])
                    ->addColumn('cities_name', Table::TYPE_TEXT, '256', [])
                    ->addColumn('wards_name', Table::TYPE_TEXT, '256', [])
                    ->setComment('Wards Address');

                $installer->getConnection()->createTable($table);
            }
        }

        $installer->endSetup();
    }
}
