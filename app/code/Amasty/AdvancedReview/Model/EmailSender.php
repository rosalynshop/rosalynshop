<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Block\Email\ReminderEmailContent;
use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Model\Email\Coupon;
use Amasty\AdvancedReview\Model\ResourceModel\Reminder\ReminderDataFactory;
use Amasty\AdvancedReview\Model\ResourceModel\ReminderProduct;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\DecoderInterface;
use Amasty\AdvancedReview\Model\OptionSource\Reminder\Status;

/**
 * Class EmailSender
 * @package Amasty\AdvancedReview\Model
 */
class EmailSender
{
    const SALT = 'amasty3488910514';

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var ReminderDataFactory
     */
    private $reminderDataFactory;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $emulation;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var \Magento\Framework\Url
     */
    private $urlBuilder;

    /**
     * @var ResourceModel\Unsubscribe\CollectionFactory
     */
    private $unsubscribeFactory;

    /**
     * @var \Amasty\AdvancedReview\Plugin\App\Config\ScopeCodeResolver
     */
    private $scopeCodeResolver;

    /**
     * @var ReminderProduct
     */
    private $reminderProduct;

    /**
     * @var Coupon
     */
    private $coupon;

    /**
     * @var Repository\ReminderRepository
     */
    private $reminderRepository;

    public function __construct(
        Config $configHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        ReminderDataFactory $reminderDataFactory,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Url $urlBuilder,
        DecoderInterface $jsonDecoder,
        \Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe\CollectionFactory $unsubscribeFactory,
        \Amasty\AdvancedReview\Plugin\App\Config\ScopeCodeResolver $scopeCodeResolver,
        ReminderProduct $reminderProduct,
        \Amasty\AdvancedReview\Model\Email\Coupon $coupon,
        \Amasty\AdvancedReview\Model\Repository\ReminderRepository $reminderRepository
    ) {
        $this->configHelper = $configHelper;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->reminderDataFactory = $reminderDataFactory;
        $this->emulation = $emulation;
        $this->appState = $appState;
        $this->layout = $layout;
        $this->jsonDecoder = $jsonDecoder;
        $this->urlBuilder = $urlBuilder;
        $this->unsubscribeFactory = $unsubscribeFactory;
        $this->scopeCodeResolver = $scopeCodeResolver;
        $this->reminderProduct = $reminderProduct;
        $this->coupon = $coupon;
        $this->reminderRepository = $reminderRepository;
    }

    /**
     * @param ReminderInterface $reminder
     * @param bool $isTestEmail
     * @param bool $force
     */
    public function send(ReminderInterface $reminder, $isTestEmail = false, $force = false)
    {
        $reminderData = $this->getReminderData($reminder);

        if (!$isTestEmail
            && !$force
            && $this->configHelper->isReminderPerCustomer()
            && $this->reminderProduct->ifCustomerExists($reminderData->getCustomerEmail())
        ) {
            return Status::CANCELED;
        }

        $sender = $this->configHelper->getModuleConfig('reminder/sender');
        $template = $this->configHelper->getModuleConfig('reminder/template');

        $storeId = $reminderData->getStoreId();
        $store = $this->storeManager->getStore($storeId);
        if ($isTestEmail) {
            $emailTo = $this->configHelper->getTestEmail();
        } else {
            $emailTo = $reminderData->getCustomerEmail();
            if ($this->isEmailIsUnsubscribed($emailTo)) {
                return Status::UNSUBSCRIBED;
            }

            if ($this->isGroupDisabled((string)$emailTo, $store->getWebsiteId())) {
                return Status::DISABLED_FOR_GROUP;
            }
        }

        $this->emulation->startEnvironmentEmulation($storeId);
        $block = $this->getContentBlock($reminderData, $isTestEmail, $force);
        if (empty($block->getProductIds())) {
            return Status::CANCELED;
        }

        if ('adminhtml' === $this->appState->getAreaCode()) {
            $productGrid = $block->toHtml(); //don't use emulate because it is failed on admin area
        } else {
            $productGrid = $this->appState->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_FRONTEND,
                [$block, 'toHtml']
            );
        }

        $data =  [
            'website_name'  => $store->getWebsite()->getName(),
            'group_name'    => $store->getGroup()->getName(),
            'store_name'    => $store->getName(),
            'customer_name' => $reminderData->getCustomerName(),
            'productGrid'   => $productGrid,
            'coupon_message' => $this->coupon->getCouponMessage($emailTo),
            'unsubscribe_link' => $this->urlBuilder->getUrl(
                'amasty_advancedreview/reminder/unsubscribe',
                [
                    'email' => $reminderData->getCustomerEmail(),
                    'hash' => $this->configHelper->hash($reminderData->getCustomerEmail() . self::SALT)
                ]
            )
        ];

        $this->scopeCodeResolver->setNeedClean(true);
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
        $this->updateCouponStatus($reminderData['entity_id']);

        $this->emulation->stopEnvironmentEmulation();
        $this->scopeCodeResolver->setNeedClean(false);

        if (!$force && !$isTestEmail) {
            $this->reminderProduct->insertData($reminderData->getCustomerEmail(), $block->getProductIds());
        }

        return Status::SENT;
    }

    /**
     * @param string $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateCouponStatus($id)
    {
        if ($this->configHelper->isAllowCoupons()) {
            $reminder = $this->reminderRepository->getById($id);
            $reminder->setCoupon(Coupon::STATUS_ACTIVE);
            $reminder->save();
        }
    }

    /**
     * @param ReminderInterface $reminder
     *
     * @return \Magento\Framework\DataObject
     * @throws LocalizedException
     */
    private function getReminderData(ReminderInterface $reminder)
    {
        $reminderItem = $this->reminderDataFactory->create()->execute($reminder->getEntityId());
        if (!$reminderItem->getEntityId()) {
            throw new LocalizedException(__('We can`t find reminder data to send email'));
        }

        return $reminderItem;
    }

    /**
     * @param $reminderData
     * @param bool $isTestEmail
     * @param bool $force
     *
     * @return ReminderEmailContent
     */
    private function getContentBlock($reminderData, $isTestEmail = false, $force = false)
    {
        /** @var ReminderEmailContent $block */
        $block = $this->layout->createBlock(ReminderEmailContent::class);
        $block->setStoreId($reminderData->getStoreId());

        $ids = $reminderData->getIds();
        $ids = explode(',', $ids);

        /* code tried to find grouped product id*/
        $productOptions = $reminderData->getProductOptions();
        $productOptions = explode('----', $productOptions);
        foreach ($productOptions as $counter => $productOption) {
            if ($productOption) {
                try {
                    $productOption = $this->jsonDecoder->decode($productOption);
                    if (isset($productOption['info_buyRequest']['super_product_config']['product_type'])
                        && isset($productOption['info_buyRequest']['super_product_config']['product_id'])
                        && $productOption['info_buyRequest']['super_product_config']['product_type'] == 'grouped'
                    ) {
                        unset($ids[$counter]);
                        $ids[] = $productOption['info_buyRequest']['super_product_config']['product_id'];
                    }
                } catch (\Exception $ex) {
                    continue;
                }
            }
        }

        if (!$isTestEmail
            && !$force
            && $this->configHelper->isReminderPerProduct()
            && ($excludeProducts = $this->reminderProduct->getProducts($reminderData->getCustomerEmail()))
        ) {
            $ids = array_diff($ids, $excludeProducts);
        }

        $ids = is_array($ids) ? array_unique($ids) : [];
        $block->setProductIds($ids);
        return $block;
    }

    /**
     * @param $emailTo
     *
     * @return bool
     */
    private function isEmailIsUnsubscribed($emailTo)
    {
        $collection = $this->unsubscribeFactory->create()
            ->addFieldToFilter('email', $emailTo)
            ->addFieldToFilter('isComment', null);

        return $collection->getSize() ? true : false;
    }

    /**
     * @param string $email
     * @return bool
     */
    private function isGroupDisabled(string $email, $websiteId = 1)
    {
        try {
            $customer = $this->customerRepository->get($email, (int)$websiteId);
            $customerGroupId = $customer->getGroupId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customerGroupId = '0';
        }

        return in_array($customerGroupId, $this->configHelper->getReminderGroups(), true);
    }
}
