<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\ResourceModel\FlushPages;

use Amasty\Fpc\Model\FlushPages;
use Amasty\Fpc\Model\ResourceModel\FlushPages as FlushPagesResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _construct()
    {
        $this->_init(FlushPages::class, FlushPagesResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
