<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\PriceDecimal\Model\Plugin\Local;

use RosalynShop\PriceDecimal\Model\Plugin\PriceFormatPluginAbstract;

/**
 * Class Format
 * @package RosalynShop\PriceDecimal\Model\Plugin\Local
 */
class Format extends PriceFormatPluginAbstract
{

    /**
     * {@inheritdoc}
     *
     * @param $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterGetPriceFormat($subject, $result)
    {
        $precision = $this->getPricePrecision();

        if ($this->getConfig()->isEnable()) {
            $result['precision'] = $precision;
            $result['requiredPrecision'] = $precision;
        }

        return $result;
    }
}
