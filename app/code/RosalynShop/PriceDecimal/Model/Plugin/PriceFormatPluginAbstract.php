<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\PriceDecimal\Model\Plugin;

use RosalynShop\PriceDecimal\Model\ConfigInterface;
use RosalynShop\PriceDecimal\Model\PricePrecisionConfigTrait;

/**
 * Class PriceFormatPluginAbstract
 * @package RosalynShop\PriceDecimal\Model\Plugin
 */
abstract class PriceFormatPluginAbstract
{

    use PricePrecisionConfigTrait;

    /** @var ConfigInterface  */
    protected $moduleConfig;

    /**
     * @param \Lillik\PriceDecimal\Model\ConfigInterface $moduleConfig
     */
    public function __construct(
        ConfigInterface $moduleConfig
    ) {
        $this->moduleConfig  = $moduleConfig;
    }
}
