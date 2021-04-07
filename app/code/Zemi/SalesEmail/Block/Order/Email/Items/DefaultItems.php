<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\SalesEmail\Block\Order\Email\Items;

use Magento\Framework\View\Element\Template;

/**
 * Class DefaultItems
 * @package Zemi\SalesEmail\Block\Order\Email\Items
 */
class DefaultItems extends \Magento\Sales\Block\Order\Email\Items\DefaultItems
{

    /**
     * @var \Zemi\Base\Helper\Data
     */
    protected $_rosalynHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * DefaultItems constructor.
     * @param Template\Context $context
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Zemi\Base\Helper\Data $rosalynHelper,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_rosalynHelper = $rosalynHelper;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @param $sku
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductImage($sku)
    {
        return $this->_rosalynHelper->getProductImage($sku);
    }

    /**
     * @param $item
     * @return string
     */
    public function getShipmentItemPrice($item)
    {
        $price = $item->getPrice();
        return $this->formatPrice($price);
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shipment');
    }

    /**
     * Format given price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getShipment()->getOrder()->formatPrice($price);
    }
}
