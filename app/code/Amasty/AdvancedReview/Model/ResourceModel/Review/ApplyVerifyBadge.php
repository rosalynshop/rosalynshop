<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel\Review;

/**
 * Class ApplyVerifyBadge
 * @package Amasty\AdvancedReview\Model\ResourceModel\Review
 */
class ApplyVerifyBadge extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $connection = $this->getConnection();

        //do not work for grouped products
        $select = $this->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(['main_table.review_id'])
            ->join(
                ['sales_order_item' => $this->getTable('sales_order_item')],
                'main_table.entity_pk_value = sales_order_item.product_id',
                []
            )->join(
                ['sales_order' => $this->getTable('sales_order')],
                'sales_order.entity_id = sales_order_item.order_id'
                . ' AND detail.customer_id=sales_order.customer_id AND sales_order.created_at < main_table.created_at',
                []
            )->group('main_table.review_id');

        $data = $connection->fetchAll($select);
        if (!empty($data)) {
            foreach ($data as &$item) {
                $item['verified_buyer'] = 1;
            }

            $connection->insertOnDuplicate(
                $this->getMainTable(),
                $data,
                ['review_id', 'verified_buyer']
            );
        }
    }
}
