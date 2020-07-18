<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Amasty\AdvancedReview\Model\Unsubscribe;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class AddUnsubscribeComment
 * @package Amasty\AdvancedReview\Setup\UpgradeSchema
 */
class AddUnsubscribeComment
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_advanced_review_unsubscribe'),
            Unsubscribe::IS_COMMENT,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => true,
                'default' => false,
                'comment' => 'Unsubscribe type'
            ]
        );
    }
}
