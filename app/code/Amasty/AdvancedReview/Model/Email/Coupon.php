<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Email;

use Amasty\AdvancedReview\Helper\Config;

class Coupon
{
    const STATUS_ACTIVE = 1;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\RuleFactory
     */
    private $ruleResourceFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        Config $configHelper,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\SalesRule\Model\ResourceModel\RuleFactory $ruleResourceFactory,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configHelper = $configHelper;
        $this->ruleFactory = $ruleFactory;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->ruleResourceFactory = $ruleResourceFactory;
        $this->logger = $logger;
    }

    /**
     * @param string $email
     * @param \Magento\Framework\DataObject $reminderData
     * @param int $status
     * @return string
     */
    public function generateCoupon($customerEmail)
    {
        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = $this->ruleFactory->create();
        $couponData = $this->generateCouponData($customerEmail, $rule);

        try {
            $rule->loadPost($couponData);
            $rule->save();
        } catch (\Exception $e) {
            $couponData['coupon_code'] = '';
            $this->logger->error($e->getMessage());
        }

        return $couponData['coupon_code'];
    }

    /**
     * @param string $customerEmail
     * @param \Magento\SalesRule\Model\Rule $rule
     * @return array
     */
    private function generateCouponData($customerEmail, $rule)
    {
        $days = (int)$this->configHelper->getModuleConfig('coupons/coupon_days');

        $couponData = [
            'name' => __('Advanced Product Reviews Coupon for %1', $customerEmail),
            'is_active' => self::STATUS_ACTIVE,
            'coupon_type' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC,
            'coupon_code' => $rule->getCouponCodeGenerator()->generateCode(),
            'stop_rules_processing' => 0,
            'uses_per_coupon' => (int)$this->configHelper->getModuleConfig('coupons/coupon_uses'),
            'uses_per_customer' => (int)$this->configHelper->getModuleConfig('coupons/uses_per_customer'),
            'from_date' => $this->date->date('Y-m-d'),
            'to_date' => $this->date->date('Y-m-d', strtotime("+$days days")),
            'simple_action' => $this->configHelper->getModuleConfig('coupons/discount_type'),
            'discount_amount' => $this->configHelper->getModuleConfig('coupons/discount_amount'),
            'website_ids' => array_keys($this->storeManager->getWebsites(true)),
            'customer_group_ids' => $this->getCustomerGroupIds()
        ];

        return array_merge($couponData, $this->generateConditions());
    }

    /**
     * @return array
     */
    protected function getCustomerGroupIds()
    {
        $customerGroupIds = [];
        if (!($customerGroupIds = $this->configHelper->getCustomerGroups())) {
            $customerGroups = $this->groupCollectionFactory->create();
            foreach ($customerGroups as $group) {
                $customerGroupIds[] = $group->getId();
            }
        }

        return $customerGroupIds;
    }

    /**
     * @return array
     */
    private function generateConditions()
    {
        $couponData['conditions'] = [
            '1' => [
                'type' => \Magento\SalesRule\Model\Rule\Condition\Combine::class,
                'aggregator' => 'all',
                'value' => 1,
                'new_child' => '',
                'conditions' =>
                    [
                        '1' => [
                            'type' => \Magento\SalesRule\Model\Rule\Condition\Address::class,
                            'attribute' => 'base_subtotal',
                            'operator' => '>=',
                            'value' => (float)$this->configHelper->getModuleConfig('coupons/min_order'),
                        ]
                    ]
            ]
        ];
        $couponData['actions'] = [
            1 => [
                'type' => \Magento\SalesRule\Model\Rule\Condition\Product\Combine::class,
                'aggregator' => 'all',
                'value' => 1,
                'new_child' => '',
            ]
        ];

        return $couponData;
    }

    /**
     * @param string $email
     * @param \Magento\Framework\DataObject $reminderData
     * @return string
     */
    public function getCouponMessage($email)
    {
        if ($this->configHelper->isAllowCoupons()) {
            if ($this->configHelper->isNeedReview()) {
                $message = $this->getReviewText();
            } else {
                $days = (int)$this->configHelper->getModuleConfig('coupons/coupon_days');
                $couponCode = $this->generateCoupon($email);
                $message = $this->getCouponText($couponCode, $days);
            }
        } else {
            $message = $this->getNoCouponText();
        }

        return $message;
    }

    /**
     * @param string $couponCode
     * @param int $days
     * @return \Magento\Framework\Phrase
     */
    protected function getCouponText($couponCode, $days)
    {
        $message = $this->getDaysMessage($days);

        return __(
            'It will take only a few minutes, just click the \'Leave a review\' button below.<br>To make the process '
            . 'more pleasant we are happy to grant you a discount coupon code, which can already be used for your next '
            . 'purchase. Here it is: %1 (%2).',
            $couponCode,
            $message->render()
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getNoCouponText()
    {
        return __('It will take only a few minutes, just click the \'Leave a review\' button below.');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getReviewText()
    {
        return __(
            'It will take only a few minutes, just click the \'Leave a review\' button below. And please kindly keep '
            . 'in mind, that you will receive a discount coupon after your review is approved.'
        );
    }

    /**
     * @param int $days
     * @return \Magento\Framework\Phrase
     */
    public function getDaysMessage(int $days)
    {
        return $days ?
            __('please kindly keep in mind that it will expire in %1 days', $days)
            : __('please keep in mind that it expires today');
    }

    /**
     * @throws \Exception
     */
    public function removeOldCoupons()
    {
        $rules = $this->getExpiredRuleCollection();
        $errors = '';
        foreach ($rules as $rule) {
            try {
                $rule->delete();
            } catch (\Exception $e) {
                $errors .= __("\r\nError when deleting rule #%s : %s", $rule->getId(), $e->getMessage());
            }
        }

        if ($errors) {
            throw new \Magento\Framework\Exception\LocalizedException($errors);
        }
    }

    /**
     * @return \Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    private function getExpiredRuleCollection()
    {
        return $this->ruleCollectionFactory->create()
            ->addFieldToFilter(
                'name',
                ['like' => __('Advanced Product Reviews Coupon for %')]
            )
            ->addFieldToFilter('to_date', ['lt' => $this->date->date('Y-m-d')]);
    }
}
