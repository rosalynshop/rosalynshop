<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Magento\Framework\Model\AbstractModel;

class FlushPages extends AbstractModel
{
    public function _construct()
    {
        $this->_init(ResourceModel\FlushPages::class);
    }
}
