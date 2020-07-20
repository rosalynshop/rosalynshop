<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */


namespace Amasty\Smtp\Helper;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var State
     */
    protected $appState;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        State $appState,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->appState = $appState;
    }

    public function getCurrentStore()
    {
        $store = $this->storeManager->getStore();

        if ($this->appState->getAreaCode() == FrontNameResolver::AREA_CODE) {
            /** @var \Magento\Sales\Model\Order $order */
            if ($order = $this->registry->registry('current_order')) {
                return $order->getStoreId();
            }

            if ($storeId = $this->registry->registry('transportBuilderPluginStoreId')) {
                return $storeId;
            }

            return 0;
        }

        return $store->getId();
    }
}
