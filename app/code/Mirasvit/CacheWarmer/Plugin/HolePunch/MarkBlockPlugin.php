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



namespace Mirasvit\CacheWarmer\Plugin\HolePunch;

use Magento\Framework\Module\FullModuleList;
use Magento\Framework\View\TemplateEngineFactory;
use Magento\Framework\View\TemplateEngineInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\CacheWarmer\Api\Service\BlockTagsGeneratorServiceInterface;
use Mirasvit\CacheWarmer\Service\BlockMarkServiceFactory;
use Mirasvit\CacheWarmer\Service\Config\HolePunchConfig;

class MarkBlockPlugin
{
    public function __construct(
        BlockMarkServiceFactory $blockMarkService,
        HolePunchConfig $holePunchConfig,
        StoreManagerInterface $storeManager,
        BlockTagsGeneratorServiceInterface $blockTagsGeneratorService,
        FullModuleList $fullModuleList
    ) {
        $this->blockMarkService          = $blockMarkService;
        $this->holePunchConfig           = $holePunchConfig;
        $this->storeManager              = $storeManager;
        $this->blockTagsGeneratorService = $blockTagsGeneratorService;
        $this->fullModuleList            = $fullModuleList;
    }

    /**
     * Mark blocks
     * @param TemplateEngineFactory   $subject
     * @param TemplateEngineInterface $invocationResult
     * @return TemplateEngineInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreate(
        TemplateEngineFactory $subject,
        TemplateEngineInterface $invocationResult
    ) {
        $storeId   = $this->storeManager->getStore()->getId();
        $templates = $this->holePunchConfig->getTemplates($storeId);
        if ($templates) {
            return $this->blockMarkService->create([
                'subject'                   => $invocationResult,
                'templates'                 => $templates,
                'blockTagsGeneratorService' => $this->blockTagsGeneratorService,
                'fullModuleList'            => $this->fullModuleList,
            ]);
        }

        return $invocationResult;
    }
}
