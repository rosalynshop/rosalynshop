<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SocialLoginPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SocialLoginPro\Plugin\Model;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;
use Mageplaza\SocialLogin\Model\SocialFactory;

/**
 * Class Social
 *
 * @package Mageplaza\SocialLogin\Model
 */
class Social
{
    /**
     * @type \\Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @type
     */
    protected $socialFactory;

    /**
     * @type \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @type \Mageplaza\SocialLogin\Helper\Social
     */
    protected $apiHelper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Social constructor.
     * @param Session $customerSession
     * @param CustomerFactory $customerFactory
     * @param SocialFactory $socialFactory
     * @param \Mageplaza\SocialLogin\Helper\Social $apiHelper
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Session $customerSession,
        CustomerFactory $customerFactory,
        SocialFactory $socialFactory,
        \Mageplaza\SocialLogin\Helper\Social $apiHelper,
        ManagerInterface $messageManager
    )
    {
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->socialFactory = $socialFactory;
        $this->apiHelper = $apiHelper;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Mageplaza\SocialLogin\Model\Social $subject
     * @param \Closure $proceed
     * @param $identify
     * @param $type
     * @return $this|mixed
     * @throws \Exception
     */
    public function aroundGetCustomerBySocial(\Mageplaza\SocialLogin\Model\Social $subject, \Closure $proceed, $identify, $type)
    {
        $customerSocial = $proceed($identify, $type);
        $customerId = $this->customerSession->getId();

        if ($customerId) {
            if (!$customerSocial->getId()) {
                $social = $this->socialFactory->create()
                    ->getCollection()->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('social_id', $identify);
                if (empty($social->getData())) {
                    try {
                        $subject->setData([
                            'social_id'              => $identify,
                            'customer_id'            => $customerId,
                            'type'                   => $type,
                            'is_send_password_email' => $this->apiHelper->canSendPassword()
                        ])
                            ->setId(null)
                            ->save();
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }
            } else {
                // 1 social account only reference to 1 store account, 1 store account can reference to many social account.
                $this->messageManager->addError(__('This social account was used to create a new store account before.'));
            }
            // If customer have multiple accounts common use a social account
            $customerSocial = $this->customerFactory->create()->load($customerId);
        }

        return $customerSocial;
    }
}
