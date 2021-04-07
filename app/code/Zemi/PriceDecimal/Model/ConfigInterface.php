<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\PriceDecimal\Model;

/**
 * Interface ConfigInterface
 * @package Zemi\PriceDecimal\Model
 */
interface ConfigInterface
{
    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig();

    /**
     * @return mixed
     */
    public function isEnable();
}
