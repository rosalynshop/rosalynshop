<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Ui\Component\Listing\Filter;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

class AddFullnameFilterToCollection implements AddFilterToCollectionInterface
{
    const TABLE_ALIAS = 'amasty_admin_user';

    const SQL_EXPRESSION = "CONCAT(amasty_admin_user.firstname, ' ' ,amasty_admin_user.lastname)";

    /**
     * Added filter by fullname to collection
     *
     * @param Collection $collection
     * @param string $field
     * @param array $condition
     */
    public function addFilter(Collection $collection, $field = null, $condition = null)
    {
        $collection->addFieldToFilter(new \Zend_Db_Expr(self::SQL_EXPRESSION), $condition);
    }
}
