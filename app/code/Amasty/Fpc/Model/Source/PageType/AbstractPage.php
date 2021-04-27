<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

abstract class AbstractPage
{
    /**
     * @var bool
     */
    protected $isMultiStoreMode;

    /**
     * @var array
     */
    protected $stores;

    public function __construct(
        $isMultiStoreMode = false,
        $stores = []
    ) {
        $this->isMultiStoreMode = $isMultiStoreMode;
        $this->stores = $stores;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    abstract public function getAllPages($limit = 0);
}
