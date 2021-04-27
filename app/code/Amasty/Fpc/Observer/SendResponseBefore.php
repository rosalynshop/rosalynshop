<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Observer;

use Amasty\Fpc\Block\Status as StatusBlock;
use Amasty\Fpc\Helper\Http as HttpHelper;
use Amasty\Fpc\Model\ActivityFactory;
use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\PageStatus;
use Amasty\Fpc\Model\Queue;
use Amasty\Fpc\Model\Repository\ActivityRepository;
use Magento\Framework\App\Helper\Context as ContextHelper;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;

class SendResponseBefore implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var BlockFactory
     */
    private $blockFactory;
    /**
     * @var PageStatus
     */
    private $pageStatus;

    /**
     * @var ActivityFactory
     */
    private $activityFactory;

    /**
     * @var ActivityRepository
     */
    private $activityRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var ContextHelper
     */
    private $contextHelper;

    /**
     * @var HttpHelper
     */
    private $httpHelper;

    public function __construct(
        Config $config,
        BlockFactory $blockFactory,
        PageStatus $pageStatus,
        ActivityFactory $activityFactory,
        ActivityRepository $activityRepository,
        StoreManagerInterface $storeManager,
        SessionManager $sessionManager,
        Queue $queue,
        ContextHelper $contextHelper,
        HttpHelper $httpHelper
    ) {
        $this->config = $config;
        $this->blockFactory = $blockFactory;
        $this->pageStatus = $pageStatus;
        $this->activityFactory = $activityFactory;
        $this->activityRepository = $activityRepository;
        $this->storeManager = $storeManager;
        $this->sessionManager = $sessionManager;
        $this->logger = $contextHelper->getLogger();
        $this->queue = $queue;
        $this->contextHelper = $contextHelper;
        $this->httpHelper = $httpHelper;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getData('request');

        if ($request->isAjax() || !$request->isGet() || $this->httpHelper->isCrawlerRequest()) {
            return;
        }

        /** @var ResponseInterface $response */
        $response = $observer->getData('response');

        if (!$response instanceof \Magento\Framework\App\Response\Http) {
            return;
        }

        try {
            if ($this->config->isLogCustomerActivity()) {
                $activity = $this->activityFactory->create();
                $activity->setStore($this->storeManager->getStore()->getId());
                $activity->setUrl($request->getUriString());
                $activity->setStatus($response->getStatusCode());
                $activity->setMobile($this->isMobile());

                $this->activityRepository->save($activity);
            }
        } catch (CouldNotSaveException $e) {
            if ($this->queue->getFlag(Queue::PROCESSING_FLAG)) {
                $this->logger->info(__('Can not save activity due the generation of queue by Warmer "
                . "because the Customers Activity selected as source.'));
            } else {
                throw $e;
            }
        }
        $status = $this->pageStatus->getStatus();
        $this->sessionManager->setPageStatus($status);

        if (!$this->config->canDisplayStatus()) {
            return;
        }

        if ($status == PageStatus::STATUS_IGNORED) { // Block already rendered
            return;
        }

        $body = $response->getBody();

        /** @var StatusBlock $block */
        $block = $this->blockFactory->createBlock(\Amasty\Fpc\Block\Status::class);
        $block->setData('status', $status);
        $html = $block->toHtml();

        $body = str_replace(StatusBlock::BLOCK_PLACEHOLDER, $html, $body);

        $response->setBody($body);
    }

    private function isMobile()
    {
        $httpUserAgent = $this->contextHelper->getHttpHeader()->getHttpUserAgent();
        if (isset($httpUserAgent) && $this->config->isProcessMobile()) {
            $regexp = $this->config->getUserAgents();

            if (preg_match('@' . $regexp . '@', $httpUserAgent)) {
                return true;
            }
        }

        return false;
    }
}
