<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var UpgradeSchema\AddReminderTable
     */
    private $addReminderTable;

    /**
     * @var UpgradeSchema\AddUnsubscribeTable
     */
    private $addUnsubscribeTable;

    /**
     * @var UpgradeSchema\AddProsConsEmailFields
     */
    private $addProsConsEmailFields;

    /**
     * @var UpgradeSchema\UpdateImageTable
     */
    private $updateImageTable;

    /**
     * @var UpgradeSchema\AddProductRelationTable
     */
    private $addProductRelationTable;

    /**
     * @var UpgradeSchema\AddVoteForeignKey
     */
    private $addVoteForeignKey;

    /**
     * @var UpgradeSchema\AddCouponField
     */
    private $addCouponField;

    /**
     * @var UpgradeSchema\AddCommentsTable
     */
    private $addCommentsTable;

    /**
     * @var UpgradeSchema\AddUnsubscribeComment
     */
    private $unsubscribeComment;

    public function __construct(
        \Amasty\AdvancedReview\Setup\UpgradeSchema\AddReminderTable $addReminderTable,
        \Amasty\AdvancedReview\Setup\UpgradeSchema\AddUnsubscribeTable $addUnsubscribeTable,
        \Amasty\AdvancedReview\Setup\UpgradeSchema\AddProsConsEmailFields $addProsConsEmailFields,
        \Amasty\AdvancedReview\Setup\UpgradeSchema\AddCouponField $addCouponField,
        UpgradeSchema\UpdateImageTable $updateImageTable,
        UpgradeSchema\AddProductRelationTable $addProductRelationTable,
        UpgradeSchema\AddCommentsTable $addCommentsTable,
        UpgradeSchema\AddVoteForeignKey $addVoteForeignKey,
        \Amasty\AdvancedReview\Setup\UpgradeSchema\AddUnsubscribeComment $unsubscribeComment
    ) {
        $this->addReminderTable = $addReminderTable;
        $this->addUnsubscribeTable = $addUnsubscribeTable;
        $this->addProsConsEmailFields = $addProsConsEmailFields;
        $this->updateImageTable = $updateImageTable;
        $this->addProductRelationTable = $addProductRelationTable;
        $this->addCouponField = $addCouponField;
        $this->addVoteForeignKey = $addVoteForeignKey;
        $this->addCommentsTable = $addCommentsTable;
        $this->unsubscribeComment = $unsubscribeComment;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addReminderTable->execute($setup);
            $this->addUnsubscribeTable->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->addProsConsEmailFields->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.4.1', '<')) {
            $this->updateImageTable->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.8.0', '<')) {
            $this->addProductRelationTable->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.10.0', '<')) {
            $this->addCouponField->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.11.0', '<')) {
            $this->addCommentsTable->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.13.0', '<')) {
            $this->unsubscribeComment->execute($setup);
        }

        $setup->endSetup();
    }
}
