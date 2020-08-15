<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Setup\UpgradeData;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Mirasvit\CacheWarmer\Api\Repository\WarmRuleRepositoryInterface;

class UpgradeData109 implements UpgradeDataInterface
{
    private $warmRuleRepository;

    public function __construct(
        WarmRuleRepositoryInterface $warmRuleRepository
    ) {
        $this->warmRuleRepository = $warmRuleRepository;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $rule = $this->warmRuleRepository->create();

        $rule->setName('Default Rule')
            ->setIsActive(1)
            ->setPriority(1);

        $this->warmRuleRepository->save($rule);
    }
}