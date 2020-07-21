<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Helper;

use Magento\Framework\Escaper;
use Magento\Backend\Model\UrlInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_objectManager;
    protected $_authSession;
    protected $_sessionManager;

    /**
     * @var array
     */
    private $orderCategories = [
        'sales/order_create',
        'sales/order',
        'admin/order_shipment',
        'sales/order_creditmemo',
        'sales/order_invoice'
    ];

    /**
     * @var array
     */
    private $showUrlCategories = [
        'catalog/product',
        'customer',
        'customer/index',
        'catalog_product_attribute',
        'customer/group',
        'catalog/product_attribute',
        'sales/order_create',
        'sales/order',
        'admin/order_shipment',
        'sales/order_creditmemo',
        'sales/order_invoice',
        'catalog_rule/promo_catalog'
    ];

    /**
     * @var \Magento\Security\Model\adminSessionInfoFactory
     */
    private $sessionInfoFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    private $context;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var UrlInterface
     */
    private $backendUrl;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Security\Model\AdminSessionInfoFactory $sessionInfoFactory,
        UrlInterface $backendUrl,
        Escaper $escaper
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->_objectManager = $objectManager;
        $this->_authSession = $authSession;
        $this->_sessionManager = $sessionManager;
        $this->sessionInfoFactory = $sessionInfoFactory;
        $this->escaper = $escaper;
        $this->backendUrl = $backendUrl;
    }

    public function getCategoryName($category)
    {
        $categoryName = $category;

        $categoriesNames = [
            'cms/page' => __('CMS Manage Pages'),
            'admin/system_config' => __('System Configuration'),
            'catalog/product' => __('Product'),
            'customer/index' => __('Customer'),
            'customer' => __('Customer'),
            'admin/system_store' => __('Store'),
            'sales/order' => __('Order'),
            'catalog/product_attribute' => __('Product Attribute'),
            'customer/group' => __('Customer Group'),
            'sales/order_create' => __('New Order'),
            'admin/user' => __('User'),
            'sales/order_invoice' => __('Invoice'),
            'admin/order_shipment' => __('Shipment'),
            'sales/order_creditmemo' => __('Credit Memo'),
            'newsletter/template' => __('Newsletter Template'),
            'admin/email_template' => __('Email Template'),
            'tax/rule' => __('Tax Rule'),
            'catalog_rule/promo_catalog' => __('Catalog Price Rule'),
            'sales_rule/promo_quote' => __('Cart Price Rule'),
            'catalog/category' => __('Product Category'),
            'search/term' => __('Search Term'),
            'admin/url_rewrite' => __('Url Rewrite'),
            'review/product' => __('Product Review'),
            'checkout/agreement' => __('Terms and Conditions'),
            'sales/order_status' => __('Order Status'),
            'tax/rate' => __('Tax Rate'),
            'admin/system_surrencysymbol' => __('Currency Symbol'),
            'catalog/product_set' => __('Attribute Set'),
            'review/rating' => __('Rating'),
            'admin/integration' => __('Integration'),
            'admin/user_role' => __('User Role'),
            'indexer/indexer' => __('Index Management'),
        ];

        if (isset($categoriesNames[$category])) {
            $categoryName = $categoriesNames[$category];
        }

        return $categoryName;
    }

    public function isOriginData($object)
    {
        $isOrigData = false;

        if ($object->getOrigData()) {
            foreach ($object->getOrigData() as $data) {
                if (!empty($data)) {
                    $isOrigData = true;
                    break;
                }
            }
        }

        return $isOrigData;
    }

    public function needToSave($object)
    {
        $needToSave = true;

        $notForSaveClasses = [
            \Amasty\AdminActionsLog\Model\Log::class,
            \Amasty\AdminActionsLog\Model\LogDetails::class,
            \Magento\Downloadable\Model\Link::class,
            \Amasty\AdminActionsLog\Model\LoginAttempts::class,
            \Amasty\AdminActionsLog\Model\ActiveSessions::class,
            \Amasty\AdminActionsLog\Model\VisitHistoryDetails::class,
            \Amasty\AdminActionsLog\Model\VisitHistory::class,
            \Magento\Ui\Model\Bookmark::class,
            \Magento\Quote\Model\Quote\Address::class,
            \Magento\Store\Model\Store::class,
            \Magento\Quote\Model\Quote\Item::class,
            \Magento\Quote\Model\Quote\Payment::class,
            \Magento\Sales\Model\Order\Item::class,
            \Magento\Sales\Model\Order\Address::class,
            \Magento\Quote\Model\Quote\Item\Option::class,
            \Magento\Quote\Model\Quote\Address\Rate::class,
            \Magento\Sales\Model\Order\Payment::class,
            \Magento\Catalog\Model\Product\Option::class,
            \Magento\Tax\Model\Sales\Order\Tax::class,
            \Magento\Sales\Model\Order\Tax\Item::class,
            \Magento\CatalogInventory\Model\Adminhtml\Stock\Item::class,
            \Magento\SalesRule\Model\Coupon::class,
            \Magento\Theme\Model\Theme::class,
            \Magento\Security\Model\AdminSessionInfo::class,
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute::class,
            \Magento\Logging\Model\Event::class,
            \Magento\Logging\Model\Event\Changes::class
        ];

        foreach ($notForSaveClasses as $class) {
            if (is_a($object, $class)) {
                return false;
            }
        }

        if (!($this->_authSession->getUser())
            || !$this->isUserInLog($this->_authSession->getUser()->getId())
            || $this->isNoCompletedOrder($object)
        ) {
            $needToSave = false;
        }

        return $needToSave;
    }

    public function isNoCompletedOrder($object)
    {
        $isNoCompletedOrder = false;

        if ($object instanceof \Magento\Quote\Model\Quote) {
            if (!$object->getReservedOrderId()) {
                $isNoCompletedOrder = true;
            }
        } elseif ($this->isOrderCreate()) {
            $isNoCompletedOrder = true;
        }

        return $isNoCompletedOrder;
    }

    public function isOrderCreate()
    {
        $isOrderCreate = false;

        $backTrace = debug_backtrace();
        foreach ($backTrace as $step) {
            if (isset($step['object'])
                && ($step['object'] instanceof \Magento\Sales\Model\AdminOrder\Create)
                && ($step['function'] == 'createOrder')
            ) {
                $isOrderCreate = true;
                break;
            }
        }
        $backTrace = null;

        return $isOrderCreate;
    }

    public function isCompletedOrder($object, $logModel)
    {
        $isCompletedOrder = false;

        if ($object instanceof \Magento\Quote\Model\Quote
            && $object->getReservedOrderId()
        ) {
            $isCompletedOrder = true;
            /**
             * @var \Amasty\AdminActionsLog\Model\LogDetails $logDetails
             */
            $logDetails = $this->_objectManager->get(\Amasty\AdminActionsLog\Model\LogDetails::class);
            $logDetails->deleteUnnecessaryOrderData($logModel);
        }

        return $isCompletedOrder;
    }

    public function needOldData($object)
    {
        $needOldData = false;

        $neededObjects = [
            \Magento\Catalog\Model\Product\Interceptor::class,
        ];

        if (in_array(get_class($object), $neededObjects)
            || !$this->isOriginData($object)) {
            $needOldData = true;
        }

        return $needOldData;
    }

    public function isUserInLog($userId)
    {
        if (!$this->scopeConfig->getValue('amaudit/log/log_all_admins')) {
            $massId = $this->scopeConfig->getValue('amaudit/log/log_admin_users');
            $massId = explode(',', $massId);
            if (in_array($userId, $massId)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function canUseGeolocation()
    {
        $canUse = false;

        if ($this->_moduleManager->isEnabled('Amasty_Geoip')) {
            /**
             * @var \Amasty\Geoip\Helper\Data $geoIpHelper
             */
            $geoIpHelper = $this->_objectManager->get(\Amasty\Geoip\Helper\Data::class);
            if ($geoIpHelper->isDone()) {
                $canUse = true;
            }
        }

        return $canUse;
    }

    public function autoClear()
    {
        $this->_objectManager->get(\Amasty\AdminActionsLog\Model\Log::class)->clearLog();
        $this->_objectManager->get(\Amasty\AdminActionsLog\Model\VisitHistory::class)->clearLog();
        $this->_objectManager->get(\Amasty\AdminActionsLog\Model\LoginAttempts::class)->clearLog();
    }

    public function getSessionId()
    {
        return $this->_sessionManager->getSessionId();
    }

    public function destroySessionById($id)
    {
        $sessionInfo = $this->sessionInfoFactory->create()->load($id, 'session_id');

        /** @var  $sessionInfo \Magento\Security\Model\AdminSessionInfo */
        $sessionInfo->getResource()->updateStatusByUserId(
            \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT_MANUALLY,
            $sessionInfo->getUserId(),
            [\Magento\Security\Model\AdminSessionInfo::LOGGED_IN]
        );
    }

    /**
     * @param \Magento\Catalog\Model\Product\Interceptor $product
     * @return array
     */
    public function _prepareProductData($product)
    {
        $extraData = [
            'current_product_id',
            'affect_product_custom_options',
            'current_store_id',
            'product_has_weight',
            'eco_collection',
            'performance_fabric',
            'new',
            'sale',
            'hide_quote_buy_button',
            'is_new',
            'is_returnable',
            'use_config_gift_message_available',
            'use_config_gift_wrapping_available',
            'use_config_is_returnable',
            'gift_message_available',
            'gift_wrapping_available',
            'url_key_create_redirect',
            'can_save_custom_options',
            'save_rewrites_history',
            'is_custom_option_changed',
            'erin_recommends',
            'msrp_display_actual_price_type',
            'options_container',
            'tax_class_id'
        ];
        $origData = $product->getOrigData();
        $newData = $product->getData();
        $data = [];

        foreach ($newData as $key => $value) {
            if (isset($origData[$key]) || is_array($value)) {
                $data[$key] = $value;
            } elseif (!in_array($key, $extraData)) {
                $data[$key] = '';
            }
        }
        $data = array_merge($newData, $data);
        $newAssociatedProductIds = $product->getAssociatedProductIds();

        if (isset($newAssociatedProductIds) && !empty($newAssociatedProductIds)) {
            $oldAssociatedProducts = $product->getTypeInstance()->getUsedProducts($product);
            $data['associated_product_ids'] = array_keys($oldAssociatedProducts);
        }

        return $data;
    }

    public function showOpenElementUrl($row)
    {
        $category = $row->getCategory();
        $url = '';
        $param = ($row->getParametrName() == 'back'
            || $row->getParametrName() == 'underfined') ? 'id' : $row->getParametrName();
        if ($row->getElementId() && $category && $row->getType() != 'Delete'
            && (in_array($category, $this->showUrlCategories))
        ) {
            if (in_array($category, $this->orderCategories)) {
                $url = $this->backendUrl
                    ->getUrl('sales/order/view', ['order_id' => $row->getElementId()]);
            } else {
                if ($category == 'customer') {
                    $category = 'customer/index';
                }
                $url = $this->backendUrl
                    ->getUrl($category . '/edit', [$param => $row->getElementId()]);
            }
        }

        $view = "";
        if ($url) {
            $view = '&nbsp<a href="' . $this->escaper->escapeUrl($url) . '"><span>[' . $this->escaper->escapeHtml(
                __('view')
            ) . ']</span></a>';
        }

        return '<span>' . $row->getItem() . '</span>' . $view;
    }
}
