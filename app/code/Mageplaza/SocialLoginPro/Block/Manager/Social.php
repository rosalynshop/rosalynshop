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

namespace Mageplaza\SocialLoginPro\Block\Manager;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\SocialLogin\Block\Popup\Social as SocialLogin;
use Mageplaza\SocialLogin\Helper\Social as SocialHelper;
use Mageplaza\SocialLogin\Model\SocialFactory as SocialFactory;

/**
 * Class Social
 *
 * @package Mageplaza\SocialLogin\Block\Popup
 */
class Social extends SocialLogin
{
    /**
     * @type \Mageplaza\SocialLogin\Model\SocialFactory
     */
    protected $socialFactory;

    /**
     * @type Session
     */
    protected $customerSession;

    /**
     * Social constructor.
     * @param Context $context
     * @param SocialHelper $socialHelper
     * @param SocialFactory $socialFactory
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        SocialHelper $socialHelper,
        SocialFactory $socialFactory,
        Session $customerSession,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        $this->socialFactory = $socialFactory;

        parent::__construct($context, $socialHelper, $data);
    }

    /**
     * @return string
     */
    public function getAvailableSocialsPro()
    {
        $availabelSocials = [];
        $socialTypes = $this->socialHelper->getSocialTypes();

        $socialCollection = $this->socialFactory->create()->getCollection()
            ->addFieldToFilter('customer_id', $this->customerSession->getCustomer()->getId());

        foreach ($socialCollection as $social) {
            $socialKey = $social->getType();
            $this->socialHelper->setType($socialKey);
            if ($this->socialHelper->isEnabled()) {
                $availabelSocials[$socialKey] = [
                    'label'     => $socialTypes[$socialKey],
                    'login_url' => $this->getLoginUrl($socialKey, ['manager' => true]),
                ];
            }
        }

        return SocialHelper::jsonEncode($availabelSocials);
    }

    /**
     * @return string
     */
    public function getManagerUrl()
    {
        return $this->getUrl('sociallogin/manager/button', ['_secure' => $this->socialHelper->isSecure()]);
    }
}



