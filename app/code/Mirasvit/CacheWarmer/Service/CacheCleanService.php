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

use Mirasvit\CacheWarmer\Api\Data\TraceInterface;
use Mirasvit\CacheWarmer\Api\Repository\TraceRepositoryInterface;
use Mirasvit\CacheWarmer\Logger\Logger;
use Mirasvit\CacheWarmer\Model\Config;

class CacheCleanService
{
    private $config;

    private $logger;

    private $traceRepository;

    public function __construct(
        Config $config,
        Logger $logger,
        TraceRepositoryInterface $traceRepository
    ) {
        $this->config          = $config;
        $this->logger          = $logger;
        $this->traceRepository = $traceRepository;
    }

    /**
     * @param string $mode
     * @param array  $tags
     */
    public function logCacheClean($mode, array $tags)
    {
        return true;
        $allowed = [
            "rma_order_status_history",
            "helpdesk_gateway",
            "rewards_transaction",
            "helpdesk_message",
            "helpdesk_ticket",
        ];
        if (count(array_intersect($allowed, $tags)) != 0) {
            return;
        }

        $isTagLogEnabled       = $this->config->isTagLogEnabled();
        $isBacktraceLogEnabled = $this->config->isBacktraceLogEnabled();

        if ($isTagLogEnabled) {
            $this->logger->debug('Clean cache', [
                'mode'      => $mode,
                'tags'      => $tags,
                'backtrace' => $isBacktraceLogEnabled ? \Magento\Framework\Debug::backtrace(true, false, false) : null,
            ]);
        }

        $url = @$_SERVER['REQUEST_URI'];

        $traceData = [
            'cli'       => php_sapi_name() == "cli" ? "Yes" : "No",
            'url'       => $url ? $url : "N/A",
            'mode'      => $mode,
            'tags'      => $tags,
            'backtrace' => \Magento\Framework\Debug::backtrace(true, false, false),
        ];

        $trace = $this->traceRepository->create();
        $trace->setEntityType(TraceInterface::ENTITY_TYPE_CACHE)
            ->setEntityId(0)
            ->setTrace($traceData)
            ->setStartedAt($this->config->getDateTime()->format(\Zend_Date::ISO_8601))
            ->setFinishedAt($this->config->getDateTime()->format(\Zend_Date::ISO_8601));

        try {
            $this->traceRepository->save($trace);
        } catch (\Exception $e) {
            // migration can be not completed yet
        }
    }
}
