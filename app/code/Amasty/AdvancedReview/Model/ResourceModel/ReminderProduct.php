<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Used for detect about which products customer already have notification.
 *
 * Class ReminderProduct
 */
class ReminderProduct extends AbstractDb
{
    const MAIN_TABLE = 'amasty_advanced_review_reminder_product';

    const CUSTOMER_EMAIL = 'customer_email';
    const PRODUCT_ID = 'product_id';

    /**
     * @return $this|void
     */
    protected function _construct()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getMainTable()
    {
        return $this->getTable(self::MAIN_TABLE);
    }

    /**
     * @param string $customerEmail
     * @param array $productIds
     *
     * @return $this
     */
    public function insertData($customerEmail, $productIds)
    {
        $data = array_map(
            function ($productId) use ($customerEmail) {
                return [
                    self::CUSTOMER_EMAIL => $customerEmail,
                    self::PRODUCT_ID => $productId
                ];
            },
            $productIds
        );
        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);

        return $this;
    }

    /**
     * @param string $customerEmail
     * @return array
     */
    public function getProducts($customerEmail)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [self::PRODUCT_ID])
            ->where(self::CUSTOMER_EMAIL . ' = ?', $customerEmail);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param string $customerEmail
     * @return bool
     */
    public function ifCustomerExists($customerEmail)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [self::CUSTOMER_EMAIL])
            ->where(self::CUSTOMER_EMAIL . ' = ?', $customerEmail);

        return (bool) $this->getConnection()->fetchOne($select);
    }
}
