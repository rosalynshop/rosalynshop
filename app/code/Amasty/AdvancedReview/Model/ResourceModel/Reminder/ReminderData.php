<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Reminder;

use Amasty\AdvancedReview\Model\Email\Coupon;
use Magento\Review\Model\Review as MagentoReview;

/**
 * Class ReminderData
 * @package Amasty\AdvancedReview\Model\ResourceModel\Reminder
 */
class ReminderData extends Collection
{
    /**
     * @param int $reminderId
     *
     * @return \Magento\Framework\DataObject
     */
    public function execute($reminderId)
    {
        $this->addFieldToFilter('main_table.entity_id', $reminderId);
        $this->joinTables()
            ->group('main_table.order_id')
            ->limit(1)
        ;

        return $this->getFirstItem();
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    public function joinTables()
    {
        return $this->getSelect()
            ->join(
                ['sales' => $this->getTable('sales_order')],
                'sales.entity_id = main_table.order_id',
                [
                    'customer_email' => 'sales.customer_email',
                    'customer_name' => 'CONCAT(sales.customer_firstname," ",sales.customer_lastname)',
                    'increment_id' => 'sales.increment_id',
                    'store_id' => 'sales.store_id'
                ]
            )
            ->joinLeft(
                ['sales_item' => $this->getTable('sales_order_item')],
                'sales_item.order_id = main_table.order_id AND sales_item.parent_item_id is NULL',
                [
                    'ids' => 'GROUP_CONCAT(sales_item.product_id)',
                    'product_options' => 'GROUP_CONCAT(sales_item.product_options SEPARATOR \'----\')'
                ]
            );
    }

    /**
     * @param string $emailTo
     * @return array
     */
    public function getReminderData($emailTo)
    {
        $select = $this->joinTables()
            ->where('customer_email=?', $emailTo)
            ->where('main_table.status=1')
            ->where('main_table.coupon=0');
        $reminderData = $this->getConnection()->fetchRow($select);

        return $reminderData;
    }
}
