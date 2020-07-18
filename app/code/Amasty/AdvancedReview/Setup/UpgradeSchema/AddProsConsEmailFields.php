<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class AddProsConsEmailFields
 * @package Amasty\AdvancedReview\Setup\UpgradeSchema
 */
class AddProsConsEmailFields
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $name = $setup->getTable('review_detail');
        $setup->getConnection()->addColumn(
            $name,
            'like_about',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'comment' => 'Customer like about this product'
            ]
        );

        $setup->getConnection()->addColumn(
            $name,
            'not_like_about',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'comment' => 'Customer do not like about this product'
            ]
        );

        $setup->getConnection()->addColumn(
            $name,
            'guest_email',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'comment' => 'Guest Email'
            ]
        );
    }
}
