<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Magento\Framework\Event\ObserverInterface;

class HandleModelDeleteBefore implements ObserverInterface
{
    protected $objectManager;
    protected $registryManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->objectManager = $objectManager;
        $this->registryManager = isset($data['registry']) ? $data['registry'] : $coreRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $object = $observer->getObject();
        $id = $object->getEntityId();
        $class = get_class($object);
        if ($id) {
            $entity = $this->objectManager->create($class)->load($id);
            $this->registryManager->register('amaudit_entity_before_delete', $entity, true);
        }
    }
}
