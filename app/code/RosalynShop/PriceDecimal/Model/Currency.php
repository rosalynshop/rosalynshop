<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\PriceDecimal\Model;

use Magento\Framework\CurrencyInterface;
use Magento\Framework\Currency as MagentoCurrency;

/**
 * Class Currency
 * @package RosalynShop\PriceDecimal\Model
 */
class Currency extends MagentoCurrency implements CurrencyInterface
{

    use PricePrecisionConfigTrait;

    /**
     * @var \RosalynShop\PriceDecimal\Model\ConfigInterface
     */
    public $moduleConfig;

    /**
     * Currency constructor.
     *
     * @param \Magento\Framework\App\CacheInterface      $appCache
     * @param \RosalynShop\PriceDecimal\Model\ConfigInterface $moduleConfig
     * @param null                                       $options
     * @param null                                       $locale
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $appCache,
        ConfigInterface $moduleConfig,
        $options = null,
        $locale = null
    ) {
        $this->moduleConfig = $moduleConfig;
        parent::__construct($appCache, $options, $locale);
    }
}
