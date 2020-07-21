<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Magento\Customer\Model\Backend\Customer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class HandleModelSaveBefore implements ObserverInterface
{
    protected $objectManager;
    protected $helper;
    protected $appState;
    protected $registryManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\AdminActionsLog\Helper\Data $helper,
        \Magento\Framework\App\State $appState
    ) {
        $this->objectManager = $objectManager;
        $this->registryManager = $coreRegistry;
        $this->helper = $helper;
        $this->appState = $appState;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
                $object = $observer->getObject();

                if ($this->helper->needToSave($object)) {
                    $this->_saveOldData($object);
                }
            }
        } catch (LocalizedException $e) {
            null;// no action is Area Code in not set
        }
    }

    protected function _saveOldData($object)
    {
        if ($this->helper->needOldData($object)) {
            if ($this->_needLoadModel($object)) {
                $class = get_class($object);
                $entity = $this->objectManager->get($class)->load($object->getId());
                $this->registryManager->register('amaudit_data_before', $entity->getData(), true);
            } else {
                $data = $object->getData();
                if ($object instanceof \Magento\Catalog\Model\Product) {
                    $data = $this->helper->_prepareProductData($object);
                }
                $this->registryManager->register('amaudit_data_before', $data, true);
                if ($object instanceof \Magento\Catalog\Model\Product\Interceptor &&
                    !empty($options = $object->getOptions())) {
                    $this->registryManager->register('amaudit_product_options_before', $object->getOptions(), true);
                }
            }
        }
    }

    protected function _needLoadModel($object)
    {
        $needLoadModel = false;

        $needLoadModelArray = [
            Customer::class,
        ];

        foreach ($needLoadModelArray as $class) {
            if (is_a($object, $class)) {
                $needLoadModel = true;
            }
        }

        return $needLoadModel;
    }
}
