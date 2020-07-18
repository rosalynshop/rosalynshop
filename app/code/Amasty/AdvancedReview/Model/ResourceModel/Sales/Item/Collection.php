<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Sales\Item;

/**
 * Class Collection
 * @package Amasty\AdvancedReview\Model\ResourceModel\Sales\Item
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Item\Collection
{
    /**
     * @param int $productId
     * @param string $customerEmail
     * @return int
     */
    public function getProductItemCount($productId, $customerEmail)
    {
        $this->join(
            ['sales_order' => $this->getTable('sales_order')],
            'sales_order.entity_id = main_table.order_id',
            'customer_email'
        );

        //for grouped products condition
        $likeCondition = '%product_id":"' . $productId . '"%';

        $this->getSelect()->where(
            "(`main_table`.`product_id`= ? OR " .
            "(`main_table`.`product_type` = 'grouped' AND `main_table`.`product_options` like '$likeCondition'))",
            $productId
        );

        $this->addFieldToFilter('sales_order.customer_email', $customerEmail);

        return $this->getSize();
    }
}
