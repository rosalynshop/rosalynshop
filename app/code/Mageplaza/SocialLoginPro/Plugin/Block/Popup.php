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

namespace Mageplaza\SocialLoginPro\Plugin\Block;

use Mageplaza\SocialLoginPro\Helper\Data;
use Mageplaza\SocialLoginPro\Model\Config\Source\Captcha;
use Mageplaza\SocialLoginPro\Model\Config\Source\RecaptchaType;

/**
 * Class Popup
 *
 * @package Mageplaza\SocialLogin\Block
 */
class Popup
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Popup constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Mageplaza\SocialLogin\Block\Popup $subject
     * @param $result
     * @return string
     */
    public function afterGetFormParams(\Mageplaza\SocialLogin\Block\Popup $subject, $result)
    {
        $params = Data::jsonDecode($result);

        if ($this->helper->isGoogleCaptcha()) {
            $params = array_merge($params, [
                'captchaInvisible' => ($this->helper->getGoogleReCaptchaType() == RecaptchaType::TYPE_INVISIBLE),
                'captchaClientKey' => $this->helper->getGoogleClientKey(),
                'captchaForms'     => $this->helper->getRecaptchaForms(),
                'isGoogleCaptcha'  => $this->helper->isGoogleCaptcha(),
                'formLoginUrl'     => $subject->getUrl('sociallogin/popup/login', ['_secure' => $subject->isSecure()])
            ]);
        } else {
            if ($this->helper->getGoogleCaptchaEnable() == Captcha::TYPE_NO) {
                $params['formLoginUrl'] = $subject->getUrl('sociallogin/popup/login', ['_secure' => $subject->isSecure()]);
            }
        }

        return Data::jsonEncode($params);
    }
}
