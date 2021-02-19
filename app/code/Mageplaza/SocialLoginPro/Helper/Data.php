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

namespace Mageplaza\SocialLoginPro\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\SocialLogin\Helper\Social as StandardHelper;
use Mageplaza\SocialLoginPro\Model\Config\Source\Captcha;

/**
 * Class Data
 *
 * @package Mageplaza\SocialLoginPro\Helper
 */
class Data extends StandardHelper
{
    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        CurlFactory $curlFactory
    )
    {
        parent::__construct($context, $objectManager, $storeManager);

        $this->curlFactory = $curlFactory;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isGoogleCaptcha($storeId = null)
    {
        $enabled = $this->getGoogleCaptchaEnable($storeId);
        if ($enabled != Captcha::TYPE_RECAPTCHA) {
            return false;
        }

        if (!$this->getGoogleClientKey($storeId) || !$this->getGoogleClientSecret($storeId)) {
            return false;
        }

        if (empty($this->getRecaptchaForms($storeId))) {
            return false;
        }

        return true;
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getGoogleCaptchaEnable($storeId = null)
    {
        return $this->getConfigGeneral('captcha/enabled', $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getGoogleClientKey($storeId = null)
    {
        return $this->getConfigGeneral('captcha/recaptcha_client_key', $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getGoogleClientSecret($storeId = null)
    {
        return $this->getConfigGeneral('captcha/recaptcha_client_secret', $storeId);
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function getRecaptchaForms($storeId = null)
    {
        $forms = $this->getConfigGeneral('captcha/recaptcha_forms', $storeId);

        return explode(',', $forms);
    }

    /**
     * @param null $storeId
     * @return string|null
     */
    public function getGoogleReCaptchaType($storeId = null)
    {
        return $this->getConfigGeneral('captcha/recaptcha_type', $storeId);
    }

    /**
     * get reCAPTCHA server response
     *
     * @param null $recaptcha
     * @return array
     */
    public function verifyResponse($recaptcha = null)
    {
        $result = ['success' => false];

        $recaptcha = $recaptcha ?: $this->_request->getParam('g-recaptcha-response');
        if (!$recaptcha) {
            $result['message'] = __('The response parameter is missing.');

            return $result;
        }

        /** @var \Magento\Framework\HTTP\Adapter\Curl $curl */
        $curl = $this->curlFactory->create();
        $curl->write(\Zend_Http_Client::POST, $this->getVerifyUrl(), '1.1', [], http_build_query([
            'secret'   => $this->getGoogleClientSecret(),
            'remoteip' => $this->_request->getClientIp(),
            'response' => $recaptcha,
        ]));

        try {
            $resultCurl = $curl->read();
            if (!empty($resultCurl)) {
                $responseBody = \Zend_Http_Response::extractBody($resultCurl);
                $responses = Data::jsonDecode($responseBody);

                if (isset($responses['success']) && $responses['success'] == true) {
                    $result['success'] = true;
                } else {
                    $result['message'] = __('The request is invalid or malformed.');
                }
            } else {
                $result['message'] = __('The request is invalid or malformed.');
            }
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
        }

        $curl->close();

        return $result;
    }

    /**
     * @return string
     */
    protected function getVerifyUrl()
    {
        return 'https://www.google.com/recaptcha/api/siteverify';
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getRedirectUrl($storeId = null)
    {
        return $this->getConfigGeneral('redirect_url', $storeId);
    }

    /**
     * @param $url
     * @return string
     */
    public function redirectUrl($url)
    {
        return $this->_getUrl($url, ['_secure' => $this->isSecure()]);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getPublicKeyOdnoklassniki($storeId = null)
    {
        $publicKey = trim($this->getConfigValue("sociallogin/odnoklassniki/public_key", $storeId));

        return $publicKey;
    }
}