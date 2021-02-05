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

namespace Mageplaza\SocialLoginPro\Plugin\Controller\Social;

/**
 * Class Login
 * @package Mageplaza\SocialLoginPro\Plugin\Controller\Social
 */
class Login
{
    /**
     * @param \Mageplaza\SocialLogin\Controller\Social\Login $subject
     * @param $result
     * @return bool
     */
    public function afterCheckCustomerLogin(\Mageplaza\SocialLogin\Controller\Social\Login $subject, $result)
    {
        $param = $subject->getRequest()->getParams();
        if (isset($param['manager'])) {
            return false;
        }

        return $result;
    }
}
