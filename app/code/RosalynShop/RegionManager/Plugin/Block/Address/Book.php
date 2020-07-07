<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Plugin\Block\Address;

/**
 * Class Grid
 * @package RosalynShop\RegionManager\Plugin\Block\Address
 */
class Book
{
    /**
     * @param \Magento\Customer\Block\Address\Book $subject
     * @param $result
     * @return string
     */
    public function afterGetTemplate(
        \Magento\Customer\Block\Address\Book $subject,
        $result
    ) {
        return 'RosalynShop_RegionManager::address/book.phtml';
    }
}
