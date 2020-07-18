<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Email;

use Magento\Framework\App\Area as Area;
use Magento\Review\Model\Review as MagentoReview;

class AdminNotificationSender
{
    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $emulation;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magento\Review\Model\Rss
     */
    private $rssModel;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Amasty\AdvancedReview\Helper\Config $config,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Review\Model\Rss $rssModel,
        \Magento\Framework\View\LayoutInterface $layout,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->emulation = $emulation;
        $this->appState = $appState;
        $this->transportBuilder = $transportBuilder;
        $this->layout = $layout;
        $this->rssModel = $rssModel;
        $this->logger = $logger;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function notify()
    {
        if ($this->config->isDailyNotificationEnabled()) {
            $this->sendDailyNotification();
        }
    }

    /**
     * @param MagentoReview $review
     */
    public function notifyInstantly(MagentoReview $review)
    {
        try {
            if ($this->config->isInstantlyNotificationEnabled()
                && 'adminhtml' !== $this->appState->getAreaCode()
            ) {
                $this->send([$review]);
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function sendDailyNotification()
    {
        $collection = $this->rssModel->getProductCollection();
        $collection->getSelect()->where('DATE(rt.created_at) = DATE(NOW())');
        $collection->getSelect()->columns('rdt.guest_email');
        $reviews = $collection->getItems();
        $this->send($reviews);
    }

    /**
     * @param array $reviews
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function send(array $reviews)
    {
        $email = $this->config->getAdminNotificationEmails();
        $sender = $this->config->getModuleConfig('admin_notifications/sender');
        $template = $this->config->getModuleConfig('admin_notifications/template');
        if (!$email || !$sender || !$template || !$reviews) {
            return false;
        }

        $storeId = $this->getReviewStoreId($reviews);
        $this->emulation->startEnvironmentEmulation($storeId);

        $transport = $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            ['area' => Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            [
                'reviewGrid' => $this->getReviewGrid($reviews)
            ]
        )->setFrom(
            $sender
        )->addTo(
            $email
        )->getTransport();

        $transport->sendMessage();
        $this->emulation->stopEnvironmentEmulation();

        return true;
    }

    /**
     * @param array $reviews
     *
     * @return string
     * @throws \Exception
     */
    protected function getReviewGrid(array $reviews)
    {
        $block = $this->getContentBlock($reviews);
        $reviewGrid = $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$block, 'toHtml']
        );

        return $reviewGrid;
    }

    /**
     * @param array $reviews
     *
     * @return int
     */
    protected function getReviewStoreId(array $reviews)
    {
        /** @var MagentoReview $review */
        $review = array_shift($reviews);

        return $review->getStoreId();
    }

    /**
     * @param array $reviews
     *
     * @return \Amasty\AdvancedReview\Block\Email\Grid
     */
    protected function getContentBlock(array $reviews)
    {
        /** @var \Amasty\AdvancedReview\Block\Email\Grid $block */
        $block = $this->layout->createBlock(\Amasty\AdvancedReview\Block\Email\Grid::class);
        $block->setReviews($reviews);

        return $block;
    }
}
