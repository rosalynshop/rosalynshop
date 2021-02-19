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

namespace Mageplaza\SocialLoginPro\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mageplaza\SocialLoginPro\Helper\Data;

/**
 * Class Manager
 * @package Mageplaza\SocialLoginPro\Observer
 */
class Manager implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Manager constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $object = $observer->getEvent()->getObject();
        if ($request->getParam('authen') == 'popup') {
            $urlRedirect = $this->helper->redirectUrl('checkout');
            $object->setUrl($urlRedirect);
        } else {
            $url = $this->helper->getRedirectUrl();
            if ($url != '') {
                $object->setUrl($url);
            }
        }
    }
}