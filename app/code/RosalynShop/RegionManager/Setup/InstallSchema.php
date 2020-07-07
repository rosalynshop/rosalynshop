<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup;

/**
 * Class InstallSchema
 * @package RosalynShop\RegionManager\Setup
 */
class InstallSchema implements Setup\InstallSchemaInterface
{
    /**
     * @param Setup\SchemaSetupInterface $setup
     * @param Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(Setup\SchemaSetupInterface $setup, Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('regionmanager_states')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'auto_increment' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'states_name',
            Table::TYPE_TEXT,
            255,
            [],
            'States name'
        )->setComment(
            'Manager States'
        );
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable(
            $setup->getTable('regionmanager_cities')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'auto_increment' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'states_name',
            Table::TYPE_TEXT,
            255,
            [],
            'States name'
        )->addColumn(
            'cities_name',
            Table::TYPE_TEXT,
            255,
            [],
            'Cities name'
        )->setComment(
            'Quan huyen'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
