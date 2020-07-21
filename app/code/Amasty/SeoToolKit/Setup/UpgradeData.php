<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Setup;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ConfigInterface
     */
    private $resourceConfig;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(ConfigInterface $resourceConfig, Filesystem $filesystem)
    {
        $this->resourceConfig = $resourceConfig;
        $this->filesystem = $filesystem;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->movePagerSettings($setup);
        }

        if (version_compare($context->getVersion(), '1.15.15', '<')) {
            $this->checkFileExisting();
        }

        $setup->endSetup();
    }

    /**
     * @throws LocalizedException
     */
    protected function checkFileExisting()
    {
        $directory = $this->filesystem->getDirectoryRead(DirectoryList::APP);
        if ($directory->isExist('code/Amasty/SeoToolKit/etc/frontend/events.xml')) {
            throw new LocalizedException(
                __("\nWARNING: This update requires removing folder app/code/Amasty/SeoToolKit.\n"
                . "Remove this folder and unpack new version of package into app/code/Amasty/SeoToolKit.\n"
                . "Run `php bin/magento setup:upgrade` again")
            );
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function movePagerSettings(ModuleDataSetupInterface $setup)
    {
        foreach (['meta_title', 'prev_next', 'meta_description'] as $field) {
            $this->updateConfigField($field);
        }
    }

    /**
     * @param string $field
     */
    private function updateConfigField($field)
    {
        $connection = $this->resourceConfig->getConnection();
        $tableName = $this->resourceConfig->getTable('core_config_data');
        $connection->update(
            $tableName,
            ['path' => "amseotoolkit/pager/" . $field],
            ["path = ?" => "amseotoolkit/general/" . $field]
        );
    }
}
