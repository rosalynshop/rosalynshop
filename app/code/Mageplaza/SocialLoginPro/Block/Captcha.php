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

namespace Mageplaza\SocialLoginPro\Block;

use Magento\Framework\View\Element\Template\Context;
use Mageplaza\SocialLoginPro\Helper\Data;
use Mageplaza\SocialLoginPro\Model\Config\Source\Captcha as CaptchaSource;
use Mageplaza\SocialLoginPro\Model\Config\Source\RecaptchaType;

/**
 * Class Captcha
 * @package Mageplaza\SocialLoginPro\Block
 */
class Captcha extends \Magento\Captcha\Block\Captcha
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Captcha constructor.
     * @param Context $context
     * @param \Magento\Captcha\Helper\Data $captchaData
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Captcha\Helper\Data $captchaData,
        Data $helperData,
        array $data = []
    )
    {
        $this->helper = $helperData;

        parent::__construct($context, $captchaData, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $enableCaptcha = $this->helper->getConfigGeneral('captcha/enabled');
        if ($enableCaptcha == CaptchaSource::TYPE_DEFAULT) {
            $blockPath = $this->_captchaData->getCaptcha($this->getFormId())->getBlockName();
            $block = $this->getLayout()->createBlock($blockPath);
            $block->setData($this->getData());
            $block->setTemplate('Magento_Captcha::default.phtml');

            return $block->toHtml();
        } else {
            if ($enableCaptcha == CaptchaSource::TYPE_RECAPTCHA) {
                $captchaType = $this->helper->getConfigGeneral('captcha/recaptcha_type');
                if ($captchaType == RecaptchaType::TYPE_NORMAL) {
                    $captchaForm = $this->helper->getConfigGeneral('captcha/recaptcha_forms');
                    if (in_array($this->getFormId(), explode(',', $captchaForm))) {
                        return '<div id="mageplaza-g-recaptcha-' . $this->getFormId() . '" class="mageplaza-g-recaptcha"></div>';
                    }
                }
            }
        }

        return '';
    }
}
