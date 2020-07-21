<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


declare(strict_types=1);

namespace Amasty\Fpc\Logger;

use Amasty\Base\Model\Serializer;
use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\Repository\FlushesLogRepository;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\Cache\FrontendInterface;

class FlushesCache extends TagScope
{
    /**
     * @var FlushesLogRepository
     */
    private $flushesLogRepository;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $backtrace;

    public function __construct(
        FrontendInterface $frontend,
        FlushesLogRepository $flushesLogRepository,
        Serializer $serializer,
        Config $config,
        string $tag
    ) {
        parent::__construct($frontend, $tag);
        $this->flushesLogRepository = $flushesLogRepository;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    public function clean($mode = \Zend_Cache::CLEANING_MODE_ALL, array $tags = [])
    {
        $this->backtrace = debug_backtrace();
        if ($this->config->isEnableFlushesLog() && $this->isNeedToSave()) {
            try {
                $trace = [];
                $flushesLogModel = $this->flushesLogRepository->getEmptyFlushesLogModel();
                $now = new \DateTime('now', new \DateTimeZone('utc'));
                $flushesLogModel->setDate($now->format('Y-m-d H:i:s'));
                foreach ($this->backtrace as $route) {
                    $trace[] = [
                        'action' => $route['class'] . $route['type'] . $route['function'] . '()'
                    ];
                }
                $flushesLogModel->setBacktrace($this->serializer->serialize($trace));
                $flushesLogModel->setDetails($this->serializer->serialize($this->getDetails($mode, $tags)));
                $this->flushesLogRepository->save($flushesLogModel);
            } catch (\Exception $e) {
                null;
            }
        }
        parent::clean($mode, $tags);
    }

    /**
     * @return bool
     */
    public function isNeedToSave(): bool
    {
        $needToSave = true;
        $ignoreClasses = $this->config->getIgnoreClasses();
        if (!is_array($ignoreClasses)) {
            return $needToSave;
        }
        foreach ($this->backtrace as $route) {
            foreach ($ignoreClasses as $class) {
                if (isset($route['class'], $class['class_name'])
                    && strpos($route['class'], $class['class_name']) !== false
                ) {
                    $needToSave = false;
                }
            }
        }

        return $needToSave;
    }

    /**
     * @param string $mode
     * @param array $tags
     *
     * @return array
     */
    public function getDetails($mode, $tags): array
    {
        $source = '';
        foreach ($this->backtrace as $item) {
            if (!empty($item['class']) && $item['class'] == \Magento\Framework\Console\Cli::class
                && !empty($item['function'])
                && $item['function'] == 'doRun'
            ) {
                $source = 'Command Line';
                break;
            }

            if (!empty($item['class'])
                && strpos($item['class'], 'Adminhtml') !== false
            ) {
                $source = 'Magento Admin';
                break;
            }

        }

        return [
            'tags'   => !empty($tags) ? implode(',', $tags) : 'No Tags',
            'mode'   => $mode,
            'source' => $source
        ];
    }
}
