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

namespace Bss\OneStepCheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package Bss\OneStepCheckout\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     * @param Context $context
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        Context $context
    ) {
        parent::__construct(
            $context
        );
        $this->productMetadata = $productMetadata;
    }

    /**
     *  Compare magento version
     *
     * @param string $version
     * @return bool
     */
    public function validateVersion($version)
    {
        $dataVersion = $this->productMetadata->getVersion();
        return version_compare($dataVersion, $version, '<');
    }

    /**
     * @param /Magento/Sales/Model/Order $order
     * @return string
     */
    public function formatDateTime($order)
    {
        return $order->getDeliveryDate();
    }
}
