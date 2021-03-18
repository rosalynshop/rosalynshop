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

namespace Mageplaza\SocialLoginPro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Captcha
 * @package Mageplaza\SocialLoginPro\Model\Config\Source
 */
class Captcha implements ArrayInterface
{
    const TYPE_NO        = 0;
    const TYPE_DEFAULT   = 1;
    const TYPE_RECAPTCHA = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::TYPE_NO        => __('No'),
            self::TYPE_DEFAULT   => __('Use store default Captcha'),
            self::TYPE_RECAPTCHA => __('Use Google ReCaptcha')
        ];
    }
}