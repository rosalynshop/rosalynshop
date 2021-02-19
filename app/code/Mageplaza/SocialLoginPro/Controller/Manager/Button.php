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

namespace Mageplaza\SocialLoginPro\Controller\Manager;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Mageplaza\SocialLogin\Model\SocialFactory;

/**
 * Class Forgot
 *
 * @package Mageplaza\SocialLogin\Controller\Popup
 */
class Button extends Action
{
    /**
     * @type \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @type \Mageplaza\SocialLogin\Model\SocialFactory
     */
    protected $socialFactory;

    /**
     * @type \\Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Button constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param SocialFactory $socialFactory
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        SocialFactory $socialFactory,
        Session $customerSession
    )
    {
        $this->socialFactory = $socialFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;

        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $result = [
            'success' => false,
            'message' => []
        ];
        $request = $this->getRequest()->getParams();
        if (!$request) {
            $result['message'] = __('Can\'t change status. Please try again!');

            return $resultJson->setData($result);
        }
        try {
            $social = $this->socialFactory->create();
            $socialCollection = $social->getCollection()->addFieldToFilter('customer_id', $this->customerSession->getCustomer()->getId())
                ->addFieldToFilter('type', $request['type'])->getAllIds();

            foreach ($socialCollection as $item) {
                $social->load($item)->delete();
            }
            $result['success'] = true;
        } catch (\Exception $e) {
            $result['message'] = __('Can\'t change status. Please try again!');
        }

        return $resultJson->setData($result);
    }
}

