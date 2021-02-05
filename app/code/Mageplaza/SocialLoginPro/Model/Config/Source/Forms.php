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
 * Class Forms
 * @package Mageplaza\SocialLoginPro\Model\Config\Source
 */
class Forms implements ArrayInterface
{
    const TYPE_LOGIN  = 'user_login';
    const TYPE_CREATE = 'user_create';
    const TYPE_FORGOT = 'user_forgotpassword';

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
            self::TYPE_LOGIN  => __('Login'),
            self::TYPE_CREATE => __('Create user'),
            self::TYPE_FORGOT => __('Forgot password')
        ];
    }
}