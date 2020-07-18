<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 * @package Amasty\AdvancedReview\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Amasty\AdvancedReview\Model\ResourceModel\Review\ApplyVerifyBadgeFactory
     */
    private $verifyBadgeFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    public function __construct(
        \Amasty\AdvancedReview\Model\ResourceModel\Review\ApplyVerifyBadgeFactory $verifyBadgeFactory,
        \Magento\Framework\App\State $appState
    ) {
        $this->verifyBadgeFactory = $verifyBadgeFactory;
        $this->appState = $appState;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            [$this, 'upgradeCallback'],
            [$setup, $context]
        );
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function upgradeCallback(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->verifyBadgeFactory->create()->execute();
        }
    }
}
