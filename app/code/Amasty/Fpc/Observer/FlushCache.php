<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Observer;

use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\Queue;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class FlushCache implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var bool
     */
    private $queueGenerated = false;

    public function __construct(
        Config $config,
        Queue $queue,
        State $appState
    ) {
        $this->config = $config;
        $this->queue = $queue;
        $this->appState = $appState;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->config->getQueueAfterGenerate()
            || !$this->config->isModuleEnabled()
            || $this->isFrontendArea()
        ) {
            return;
        }

        try {
            $this->appState->setAreaCode(Area::AREA_GLOBAL);
        } catch (\Exception $e) {
            null;
            //launched from admin
            //(emulateArea not working due the area emulation in \Amasty\Fpc\Model\Source\PageType\Emulated)
        }

        $this->queue->forceUnlock();

        if (!$this->queueGenerated) {
            list($result, $processedItems) = $this->queue->generate();
            $this->queueGenerated = $result;
        }
    }

    /**
     * @return bool
     */
    private function isFrontendArea()
    {
        $isFrontend = true;

        try {
            $isFrontend = $this->appState->getAreaCode() == Area::AREA_FRONTEND;
        } catch (LocalizedException $e) {
            null;
        }

        return $isFrontend;
    }
}
