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



namespace Mirasvit\CacheWarmer\Service;

use Magento\Framework\Stdlib\DateTime;
use Mirasvit\CacheWarmer\Api\Data\JobInterface;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Data\WarmRuleInterface;
use Mirasvit\CacheWarmer\Api\Repository\JobRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\WarmRuleRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Service\JobServiceInterface;
use Mirasvit\CacheWarmer\Api\Service\WarmerServiceInterface;
use Mirasvit\CacheWarmer\Logger\Logger;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Service\Rate\CacheFillRateService;
use Mirasvit\CacheWarmer\Service\Rate\ServerLoadRateService;
use Mirasvit\CacheWarmer\Service\Warmer\PageWarmStatus;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class JobService implements JobServiceInterface
{
    const MAX_ERRORS   = 3;
    const MAX_ATTEMPTS = 3;

    /**
     * @var JobRepositoryInterface
     */
    private $jobRepository;

    /**
     * @var CacheFillRateService
     */
    private $cacheFillRateService;

    /**
     * @var ServerLoadRateService
     */
    private $serverLoadRateService;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var WarmerServiceInterface
     */
    private $warmerService;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        JobRepositoryInterface $jobRepository,
        PageRepositoryInterface $pageRepository,
        WarmerServiceInterface $warmerService,
        CacheFillRateService $cacheFillRateService,
        ServerLoadRateService $serverLoadRateService,
        WarmRuleService $warmRuleService,
        WarmRuleRepositoryInterface $ruleRepository,
        Config $config,
        Logger $logger
    ) {
        $this->jobRepository  = $jobRepository;
        $this->pageRepository = $pageRepository;
        $this->ruleRepository = $ruleRepository;

        $this->warmerService         = $warmerService;
        $this->warmRuleService       = $warmRuleService;
        $this->cacheFillRateService  = $cacheFillRateService;
        $this->serverLoadRateService = $serverLoadRateService;

        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run(JobInterface $job)
    {
        $ts = microtime(true);

        $this->startJob($job);

        if (!$this->canRunJob()) {
            return $this->finishJob($job, JobInterface::STATUS_MISSED);
        }

        $this->warmRuleService->refreshPagesByRules();

        $jobRuleCollection = $this->ruleRepository->getCollection();
        $jobRuleCollection->addFieldToFilter(WarmRuleInterface::IS_ACTIVE, 1);
        $jobRuleCollection->setOrder(WarmRuleInterface::PRIORITY, "DESC");

        foreach ($jobRuleCollection as $rule) {

            $pages = $this->getPageCollection($rule);

            $errorCounter = 0;
            foreach ($this->warmerService->warmCollection($pages, $rule) as $status) {
                $this->logWarmStatus($job, $status);
                $this->handlePageStatus($status);

                if ($status->isError() && !$status->isSoftError()) {
                    $errorCounter++;

                    if ($errorCounter >= self::MAX_ERRORS) {
                        $this->logError($job, 'Stopped execution. Reached errors limit.', [$errorCounter]);

                        return $this->finishJob($job, JobInterface::STATUS_ERROR);
                    }
                }


                if ($this->isTimeout($ts)) {
                    break;
                }
            }
        }

        $this->logger->info('Execution Time', [round(microtime(true) - $ts, 1)]);

        $this->finishJob($job);

        return $this;
    }

    /**
     * @param JobInterface $job
     * @return $this
     */
    private function startJob($job)
    {
        $this->logger->setJob($job);

        set_error_handler([$this, 'errorHandler']);

        $this->logger->info('Start job');

        $job->setStartedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
            ->setStatus(JobInterface::STATUS_RUNNING);

        $this->jobRepository->save($job);

        $this->logCacheFillRate($job)
            ->logServerLoadRate($job);

        return $this;
    }

    /**
     * @param JobInterface $job
     * @return $this
     */
    private function logServerLoadRate($job)
    {
        $message = 'Server Load Rate';
        $rate    = $this->serverLoadRateService->getRate();

        $this->logger->info($message, [$rate]);

        $info             = $job->getInfo();
        $info[$message][] = $rate . '%';
        $job->setInfo($info);

        $this->jobRepository->save($job);

        return $this;
    }

    /**
     * @param JobInterface $job
     * @return $this
     */
    private function logCacheFillRate($job)
    {
        $message = 'Cache Fill Rate';
        $rate    = $this->cacheFillRateService->getRate();

        $this->logger->info($message, [$rate]);

        $info             = $job->getInfo();
        $info[$message][] = $rate . '%';
        $job->setInfo($info);

        $this->jobRepository->save($job);

        return $this;
    }

    /**
     * @return bool
     */
    private function canRunJob()
    {
        if (!$this->config->isPageCacheEnabled()) {
            $this->logger->warning('Page Cache is disabled');

            return false;
        }

        $serverLoadRate      = $this->serverLoadRateService->getRate();
        $serverLoadThreshold = $this->config->getServerLoadThreshold();

        if ($serverLoadRate > $serverLoadThreshold) {
            $this->logger->warning('Server load threshold reached', [
                'rate'      => $serverLoadRate,
                'threshold' => $serverLoadThreshold,
            ]);

            return false;
        }

        $cacheFillRate      = $this->cacheFillRateService->getRate();
        $cacheFillThreshold = $this->config->getCacheFillThreshold();

        if ($cacheFillRate > $cacheFillThreshold) {
            $this->logger->warning('Cache fill threshold reached', [
                'rate'      => $cacheFillRate,
                'threshold' => $cacheFillThreshold,
            ]);

            return false;
        }

        return true;
    }

    /**
     * @param JobInterface $job
     * @param string       $status
     * @return $this
     */
    private function finishJob($job, $status = null)
    {
        $job->setFinishedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));

        if (!$status) {
            $status = JobInterface::STATUS_COMPLETED;
        }

        $job->setStatus($status);

        $this->jobRepository->save($job);

        $this->logCacheFillRate($job)
            ->logServerLoadRate($job);

        $this->logger->info('Finish job');

        restore_error_handler();

        return $this;
    }


    /**
     * @param JobInterface   $job
     * @param PageWarmStatus $status
     * @return $this
     */
    private function logWarmStatus($job, $status)
    {
        $message = 'Warmed Pages';
        $this->logger->info($status->toString());

        $info           = $job->getInfo();
        $info[$message] = isset($info[$message]) ? $info[$message] + 1 : 1;
        $job->setInfo($info);

        return $this;
    }

    /**
     * @param JobInterface $job
     * @param string       $message
     * @param array|null   $context
     * @return $this
     */
    private function logError($job, $message, array $context = null)
    {
        $this->logger->error($message, $context);

        $info          = $job->getInfo();
        $info['Error'] = $message;
        $job->setInfo($info);

        $this->jobRepository->save($job);

        return $this;
    }

    /**
     * @param PageWarmStatus $status
     * @return $this
     */
    private function handlePageStatus(PageWarmStatus $status)
    {
        $page = $status->getPage();

        if ($status->isSoftError()) {
            if ($page->getAttempts() > self::MAX_ATTEMPTS) {
                $this->logger->warning('Remove page. Soft error.', [$page->getUri()]);
                $this->pageRepository->delete($page);
            } else {
                $page->setAttempts($page->getAttempts() + 1);
                $this->pageRepository->save($page);
            }
        } elseif ($page->getAttempts() > 0) {
            $page->setAttempts(0);
            $this->pageRepository->save($page);
        }

        return $this;
    }

    /**
     * @param int $startTime
     * @return bool
     */
    private function isTimeout($startTime)
    {
        return (microtime(true) - $startTime) > $this->config->getJobRunThreshold();
    }

    /**
     * @param string $type
     * @param string $msg
     * @return void
     */
    public function errorHandler($type, $msg)
    {
        $this->logger->err($msg);

        $job = $this->logger->getJob();
        $this->finishJob($job, JobInterface::STATUS_ERROR);
    }

    private function getPageCollection(WarmRuleInterface $rule)
    {
        $collection = $this->pageRepository->getCollection();
        $collection->getSelect()->where("FIND_IN_SET(?,warm_rule_ids)", $rule->getId());
        $collection->setOrder(PageInterface::POPULARITY, 'desc');

        return $collection;
    }
}
