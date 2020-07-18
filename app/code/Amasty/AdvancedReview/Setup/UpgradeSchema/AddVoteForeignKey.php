<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Magento\Framework\Setup\SchemaSetupInterface;

class AddVoteForeignKey
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $voteTable = $setup->getTable('amasty_advanced_review_vote');

        $setup->getConnection()->changeColumn(
            $voteTable,
            'review_id',
            'review_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                'length' => null,
                'nullable' => false,
                'unsigned' => true,
                'comment' => 'Review ID'
            ]
        );
        $setup->getConnection()->addForeignKey(
            $setup->getFkName('amasty_advanced_review_vote', 'review_id', 'review', 'review_id'),
            $voteTable,
            'review_id',
            $setup->getTable('review'),
            'review_id'
        );
    }
}