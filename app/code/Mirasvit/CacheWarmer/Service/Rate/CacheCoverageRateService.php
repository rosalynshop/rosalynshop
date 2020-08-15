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

use Magento\Variable\Model\VariableFactory;
use Mirasvit\CacheWarmer\Api\Data\LogInterface;
use Mirasvit\CacheWarmer\Api\Repository\LogRepositoryInterface;
use Mirasvit\CacheWarmer\Model\Config;

class CacheCoverageRateService extends AbstractRate
{
    const VARIABLE_CODE = 'mst_cache_warmer_coverage_rate_v2';

    /**
     * @var LogRepositoryInterface
     */
    private $logRepository;

    public function __construct(
        LogRepositoryInterface $logRepository,
        VariableFactory $variableFactory,
        Config $config
    ) {
        $this->logRepository = $logRepository;

        parent::__construct($variableFactory, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getRate()
    {
        $collection = $this->logRepository->getCollection();

        $collection->getSelect()->columns(['rate' => new \Zend_Db_Expr('SUM(is_hit) / COUNT(is_hit)')]);
        $collection->addFieldToFilter(LogInterface::CREATED_AT, [
            'gt' => date('Y-m-d H:i:s', time() - 24 * 60 * 60),
        ]);

        $rate = round($collection->getFirstItem()->getData('rate') * 100);

        return $rate;
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
