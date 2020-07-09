<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Plugin\Model\Quote;

use Bss\OneStepCheckout\Helper\Config;

/**
 * Class Address
 *
 * @package Bss\OneStepCheckout\Plugin\Model\Quote
 */
class Address
{
    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @param Config $configHelper
     */
    public function __construct(
        Config $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * @param /Magento\Quote\Model\Quote\Address $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetRegionId(
        \Magento\Quote\Model\Quote\Address $subject,
        $result
    ) {
        if ($result && $this->configHelper->isEnabled()) {
            $countryModel = $subject->getCountryModel();
            $regionCollection = $countryModel->getRegionCollection();
            $region = $subject->getRegion();
            $allowedRegions = $regionCollection->getAllIds();
            if (!in_array($result, $allowedRegions) && $region) {
                $result = false;
            }
        }
        return $result;
    }
}
