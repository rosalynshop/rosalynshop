<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Ui\Component\Listing\Filter;

use Magento\Framework\Data\Collection;
use Magento\Framework\DB\Select;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

class AddUsernameFilterToCollection implements AddFilterToCollectionInterface
{
    /**
     * Added filter by username to collection
     *
     * @param Collection $collection
     * @param string $field
     * @param array $condition
     */
    public function addFilter(Collection $collection, $field = null, $condition = null)
    {
        $collection->addFieldToFilter('main_table.' . $field, $condition);
    }
}
