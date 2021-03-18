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

namespace Mageplaza\SocialLoginPro\Plugin\Helper;

use Mageplaza\SocialLoginPro\Helper\Data as HelperData;

/**
 * Class Social
 *
 * @package Mageplaza\SocialLoginPro\Plugin\Helper
 */
class Social
{
    /**
     * @var \Mageplaza\SocialLoginPro\Helper\Data
     */
    protected $helperData;

    /**
     * Social constructor.
     * @param \Mageplaza\SocialLoginPro\Helper\Data $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @param \Mageplaza\SocialLogin\Helper\Social $subject
     * @param array $result
     * @return array
     */
    public function after_getSocialTypes(\Mageplaza\SocialLogin\Helper\Social $subject, array $result)
    {
        return array_merge($result, [
            'disqus'        => 'Disqus',
            'mailru'        => 'Mailru',
            'odnoklassniki' => 'Odnoklassniki',
            'steam'         => 'Steam'
        ]);
    }

    /**
     * @param \Mageplaza\SocialLogin\Helper\Social $subject
     * @param \Closure $proceed
     * @param $type
     * @return mixed
     */
    public function aroundGetSocialConfig(\Mageplaza\SocialLogin\Helper\Social $subject, \Closure $proceed, $type)
    {
        $result = $proceed($type);
        if (empty($result)) {
            $apiData = [
                'Disqus'        => ['wrapper' => ['class' => '\Mageplaza\SocialLoginPro\Model\Providers\Disqus']],
                'Mailru'        => ['wrapper' => ['class' => '\Mageplaza\SocialLoginPro\Model\Providers\Mailru']],
                'Odnoklassniki' => [
                    'wrapper'    => ['class' => '\Mageplaza\SocialLoginPro\Model\Providers\Odnoklassniki'],
                    'public_key' => $this->helperData->getPublicKeyOdnoklassniki()
                ],
                'Steam'         => ['wrapper' => ['class' => '\Mageplaza\SocialLoginPro\Model\Providers\Steam']]
            ];
            if ($type && array_key_exists($type, $apiData)) {
                return $apiData[$type];
            }
        }

        return $result;
    }
}
