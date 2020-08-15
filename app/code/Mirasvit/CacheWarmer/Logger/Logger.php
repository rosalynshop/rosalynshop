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



namespace Mirasvit\CacheWarmer\Logger;

use Mirasvit\CacheWarmer\Api\Data\JobInterface;
use Mirasvit\CacheWarmer\Api\Repository\JobRepositoryInterface;

class Logger extends \Monolog\Logger
{
    const DELIMITER = '##';

    /**
     * @var JobInterface
     */
    private static $job;

    /**
     * @var JobRepositoryInterface
     */
    private $jobRepository;

    public function __construct(
        JobRepositoryInterface $jobRepository,
        $name,
        $handlers = [],
        $processors = []
    ) {
        $this->jobRepository = $jobRepository;

        parent::__construct($name, $handlers, $processors);
    }

    /**
     * @param JobInterface $job
     * @return $this
     */
    public function setJob(JobInterface $job)
    {
        self::$job = $job;

        return $this;
    }

    /**
     * @return JobInterface
     */
    public function getJob()
    {
        return self::$job;
    }

    /**
     * {@inheritdoc}
     */
    public function addRecord($level, $message, array $context = [])
    {
        $job = self::$job;
        if ($job) {
            $trace = $job->getTrace();

            $traceKey     = (new \DateTime())->format('H:m:s.') . microtime(true);
            $traceMessage = self::$levels[$level]
                . self::DELIMITER . $message
                . self::DELIMITER . ($context ? \Zend_Json::encode($context) : '');

            $trace[$traceKey] = $traceMessage;

            $job->setTrace($trace);

            $this->jobRepository->save($job);

            $context['job'] = $job->getId();
        }

        return parent::addRecord($level, $message, $context);
    }
}
