<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Plugin\Model\Checkout;

use Bss\OneStepCheckout\Model\AdditionalData;
use Magento\Quote\Api\CartRepositoryInterface;
use Bss\OneStepCheckout\Helper\Config;

/**
 * Class CustomerAdditionalData
 *
 * @package Bss\OneStepCheckout\Plugin\Model\Checkout
 */
class CustomerAdditionalData
{
    /**
     * @var AdditionalData
     */
    private $additionalDataModel;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @param AdditionalData $additionalDataModel
     * @param CartRepositoryInterface $cartRepository
     * @param Config $configHelper
     */
    public function __construct(
        AdditionalData $additionalDataModel,
        CartRepositoryInterface $cartRepository,
        Config $configHelper
    ) {
        $this->additionalDataModel = $additionalDataModel;
        $this->cartRepository = $cartRepository;
        $this->configHelper = $configHelper;
    }

    /**
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $subject
     * @param callable $proceed
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     * @return mixed
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($paymentMethod->getExtensionAttributes() !== null
            && $this->configHelper->isEnabled()
            && $paymentMethod->getExtensionAttributes()->getBssOsc() !== null
        ) {
            $additionalData = $paymentMethod->getExtensionAttributes()->getBssOsc();
            $orderId = $proceed($cartId, $paymentMethod, $billingAddress);
            if (!empty($additionalData) && isset($additionalData['order_comment'])) {
                $this->additionalDataModel->saveComment($orderId, $additionalData);
            }
            if (!empty($additionalData)
                && $this->configHelper->isDisplayField('enable_subscribe_newsletter')
            ) {
                $this->additionalDataModel->subscriber($orderId, $additionalData);
            }
        } else {
            return $proceed($cartId, $paymentMethod, $billingAddress);
        }
    }

    /**
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     * @return mixed
     */
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($paymentMethod->getExtensionAttributes() !== null
            && $this->configHelper->isEnabled()
            && $paymentMethod->getExtensionAttributes()->getBssOsc() !== null
        ) {
            $quote = $this->cartRepository->getActive($cartId);
            $additionalData = $paymentMethod->getExtensionAttributes()->getBssOsc();
            if (!empty($additionalData)) {
                $this->additionalDataModel->saveDelivery($quote, $additionalData);
            }
        }
    }
}
