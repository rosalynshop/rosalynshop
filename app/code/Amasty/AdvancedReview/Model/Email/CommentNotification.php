<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Email;

use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Model\Unsubscribe;

class CommentNotification
{
    const SALT = 'amasty131203102019';

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var \Amasty\AdvancedReview\Model\ResourceModel\Review\Collection
     */
    private $collection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private $productCollection;

    /**
     * @var \Magento\Framework\Url
     */
    private $urlBuilder;

    /**
     * @var \Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe\Collection
     */
    private $unsubscribeCollection;

    public function __construct(
        Config $configHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Amasty\AdvancedReview\Model\ResourceModel\Review\Collection $collection,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Magento\Framework\Url $urlBuilder,
        \Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe\Collection $unsubscribeCollection
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->configHelper = $configHelper;
        $this->collection = $collection;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->productCollection = $productCollection;
        $this->urlBuilder = $urlBuilder;
        $this->unsubscribeCollection = $unsubscribeCollection;
    }

    /**
     * @param \Amasty\AdvancedReview\Api\Data\CommentInterface $reviewComment
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendMessage(\Amasty\AdvancedReview\Api\Data\CommentInterface $reviewComment)
    {
        $sender = $this->configHelper->getModuleConfig('comments/sender');
        $template = $this->configHelper->getModuleConfig('comments/template');
        $store = $this->storeManager->getStore($reviewComment->getStoreId());
        $review = $this->collection->getItemById($reviewComment->getReviewId());
        $productUrl = $review->getProductUrl($review->getEntityPkValue(), $store);
        $emailTo = $this->getEmail($review);

        if (!$emailTo || !$this->isNeedSend($emailTo)) {
            return false;
        }

        $data = [
            'customer_name' => $review->getNickname(),
            'title_review' => $review->getTitle(),
            'review' => $review->getDetail(),
            'comment' => $reviewComment->getMessage(),
            'link' => $productUrl . '#reviews',
            'store_name' => $store->getName(),
            'unsubscribe_link' => $this->urlBuilder->getUrl(
                'amasty_advancedreview/comment/unsubscribe',
                [
                    'email' => $emailTo,
                    'hash' => $this->configHelper->hash($emailTo . self::SALT)
                ]
            )
        ];

        $this->send($template, $store, $data, $sender, $emailTo);
    }

    /**
     * @param string $emailTo
     * @return bool
     */
    private function isNeedSend($emailTo = '')
    {
        $this->unsubscribeCollection
            ->addFieldToFilter(Unsubscribe::EMAIL, $emailTo)
            ->addFieldToFilter(Unsubscribe::IS_COMMENT, true);

        return !$this->unsubscribeCollection->getSize();
    }

    /**
     * @param $review
     * @return string|null
     */
    private function getEmail($review)
    {
        try {
            $customer = $this->customerRepository->getById($review->getCustomerId());
            $emailTo = $customer->getEmail();
        } catch (\Exception $ex) {
            $emailTo = null;
        }

        $guestEmail = $review->getGuestEmail();
        if (!$emailTo && $guestEmail) {
            $emailTo = $guestEmail;
        }

        return $emailTo;
    }

    /**
     * @param string $template
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param array $data
     * @param array|string $sender
     * @param array|string $emailTo
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    private function send($template, $store, $data, $sender, $emailTo)
    {
        $transport = $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getId()]
        )->setTemplateVars(
            $data
        )->setFrom(
            $sender
        )->addTo(
            $emailTo
        )->getTransport();

        $transport->sendMessage();
    }
}
