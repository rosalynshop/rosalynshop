<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\Sales\Model\ResourceModel;

use Amasty\AdvancedReview\Plugin\Sales\Model\Service\OrderService;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\ResourceModel\Order as OrderSubject;

/**
 * Class Order
 * @package Amasty\AdvancedReview\Plugin\Sales\Model\ResourceModel
 */
class Order extends OrderService
{
    /**
     * @param OrderSubject $subject
     * @param AbstractModel $object
     *
     * @return array
     */
    public function aroundSave(OrderSubject $subject, \Closure $proceed, AbstractModel $object)
    {
        $statusBefore = $object->getOrigData('status');
        $result = $proceed($object);

        if ($this->config->isReminderEnabled()
            && (bool)$this->config->getTriggerOrderStatus()
            && $object->getStatus() != $statusBefore
            && in_array($object->getStatus(), $this->config->getTriggerOrderStatus())
        ) {
            $this->saveOrderToReminder($object);
        }

        return $result;
    }
}
