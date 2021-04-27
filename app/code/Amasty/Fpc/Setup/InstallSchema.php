<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateActivityTable
     */
    private $createActivityTable;

    /**
     * @var Operation\CreateQueueTable
     */
    private $createQueueTable;

    /**
     * @var Operation\CreateLogTable
     */
    private $createLogTable;

    public function __construct(
        Operation\CreateQueueTable $createQueueTable,
        Operation\CreateLogTable $createLogTable,
        Operation\CreateActivityTable $createActivityTable
    ) {
        $this->createQueueTable = $createQueueTable;
        $this->createLogTable = $createLogTable;
        $this->createActivityTable = $createActivityTable;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $this->createQueueTable->execute($setup);
        $this->createLogTable->execute($setup);
        $this->createActivityTable->execute($setup);

        $installer->endSetup();
    }
}
