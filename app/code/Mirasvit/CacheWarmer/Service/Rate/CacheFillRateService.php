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



namespace Mirasvit\CacheWarmer\Service\Rate;

use Magento\Framework\App\CacheInterface;
use Magento\Variable\Model\VariableFactory;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Service\PageService;

class CacheFillRateService extends AbstractRate
{
    const VARIABLE_CODE = 'mst_cache_warmer_cache_fill_rate_v2';

    private $pageService;

    private $pageRepository;

    private $cache;

    public function __construct(
        PageService $pageService,
        PageRepositoryInterface $pageRepository,
        CacheInterface $cache,
        VariableFactory $variableFactory,
        Config $config
    ) {
        $this->pageService    = $pageService;
        $this->pageRepository = $pageRepository;
        $this->cache          = $cache;

        parent::__construct($variableFactory, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getRate()
    {
        $collection = $this->getIndicatingPages();

        $ts     = null;
        $total  = 0;
        $cached = 0;

        /** @var \Mirasvit\CacheWarmer\Model\Page $page */
        while ($page = $collection->fetchItem()) {
            if ($ts == null) {
                $ts = microtime(true);
            }

            if ($page->getCacheId() && $this->pageService->isCached($page)) {
                $cached++;
            }

            $total++;

            if (microtime(true) - $ts > 2 && $total > 20) {
                break;
            }
        }

        if ($total == 0) {
            $total = 1;
        }

        return round($cached / $total * 100);
    }

    /**
     * @return \Mirasvit\CacheWarmer\Model\ResourceModel\Page\Collection
     */
    public function getIndicatingPages()
    {
        $identifier = 'cache_warmer_fill_rate_indicating_ids';

        $ids = $this->cache->load($identifier);

        if ($ids) {
            $ids = \Zend_Json::decode($ids);
        } else {
            $collection = $this->pageRepository->getCollection();
            $collection->getSelect()
                ->limit(2000)
                ->orderRand();

            $ids = $collection->getColumnValues(PageInterface::ID);

            $this->cache->save(\Zend_Json::encode($ids), $identifier, [], 600);
        }

        $ids[] = 0;

        $collection = $this->pageRepository->getCollection();
        $collection->getSelect()->where('page_id IN(' . implode(',', $ids) . ')');

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function saveToHistory($rate)
    {
        return parent::saveRateToHistory($rate, self::VARIABLE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getHistory()
    {
        return parent::getRateHistory(self::VARIABLE_CODE);
    }
}
