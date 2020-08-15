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



namespace Mirasvit\CacheWarmer\Setup\UpgradeSchema;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;

class UpgradeSchema108 implements UpgradeSchemaInterface
{
    private $pageRepository;

    public function __construct(
        PageRepositoryInterface $pageRepository
    ) {
        $this->pageRepository = $pageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $offset = 0;
        $limit  = 1000;

        $pageCollection = $this->pageRepository->getCollection();

        $select = $pageCollection->getSelect();
        $select->limit($limit, $offset);

        while ($pageCollection->count()) {
            /** @var PageInterface $page */
            foreach ($pageCollection as $page) {
                $varyData = $page->getData(PageInterface::VARY_DATA);

                $varyData = @unserialize($varyData);

                if (is_array($varyData)) {
                    $page->setVaryData($varyData);
                    $this->pageRepository->save($page);
                }
            }
            $pageCollection->clear();
            $offset += $limit;
            $select->limit($limit, $offset);
        }
    }
}
