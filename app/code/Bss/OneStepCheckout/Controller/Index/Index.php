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

namespace Bss\OneStepCheckout\Controller\Index;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Checkout\Controller\Onepage;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\LayoutFactory as ResultLayout;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Session as SessionModel;
use Bss\OneStepCheckout\Helper\Config;

/**
 * Class Index
 *
 * @package Bss\OneStepCheckout\Controller\Index
 */
class Index extends Onepage
{
    /**
     * Checkout helper
     *
     * @var Data
     */
    private $checkoutHelper;

    /**
     * Checkout session
     *
     * @var Session
     */
    private $checkoutSession;

    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param Registry $coreRegistry
     * @param InlineInterface $translateInline
     * @param Validator $formKeyValidator
     * @param ScopeConfigInterface $scopeConfig
     * @param LayoutFactory $layoutFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param PageFactory $resultPageFactory
     * @param ResultLayout $resultLayoutFactory
     * @param RawFactory $resultRawFactory
     * @param JsonFactory $resultJsonFactory
     * @param Data $checkoutHelper
     * @param SessionModel $checkoutSession
     * @param Config $configHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        Registry $coreRegistry,
        InlineInterface $translateInline,
        Validator $formKeyValidator,
        ScopeConfigInterface $scopeConfig,
        LayoutFactory $layoutFactory,
        CartRepositoryInterface $quoteRepository,
        PageFactory $resultPageFactory,
        ResultLayout $resultLayoutFactory,
        RawFactory $resultRawFactory,
        JsonFactory $resultJsonFactory,
        Data $checkoutHelper,
        SessionModel $checkoutSession,
        Config $configHelper
    ) {
        $this->checkoutHelper = $checkoutHelper;
        $this->checkoutSession = $checkoutSession;
        $this->configHelper = $configHelper;
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory
        );
    }

    /**
     * Checkout page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->configHelper->isEnabled()) {
            return $this->resultRedirectFactory->create()->setPath('checkout');
        }

        if (!$this->checkoutHelper->canOnepageCheckout()) {
            $this->messageManager->addErrorMessage(__('One-page checkout is turned off.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        if (!$this->_customerSession->isLoggedIn() && !$this->checkoutHelper->isAllowedGuestCheckout($quote)) {
            $this->messageManager->addErrorMessage(__('Guest checkout is disabled.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $currentUrl = $this->_url->getUrl('*/*/*', ['_secure' => true]);
        $this->_customerSession->setBeforeAuthUrl($currentUrl);
        $this->_customerSession->regenerateId();

        $this->checkoutSession->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();
        $resultPage = $this->resultPageFactory->create();
        if ($tilte = $this->configHelper->getGeneral('tilte')) {
            $resultPage->getConfig()->getTitle()->set($tilte);
        }
        return $resultPage;
    }
}
