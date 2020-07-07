<?php
/**
 * @author   Rosalynshop <info@rosalynshop.com>
 * @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\SalesEmail\Block\Order\Email\Shipment;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Sales Order Email Shipment items
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Sales\Block\Order\Email\Shipment\Items
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Items constructor.
     * @param Template\Context $context
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->_imageHelper = $imageHelper;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * Prepare item before output
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $renderer
     * @return void
     */
    protected function _prepareItem(\Magento\Framework\View\Element\AbstractBlock $renderer)
    {
        $renderer->getItem()->setOrder($this->getOrder());
        $renderer->getItem()->setSource($this->getShipment());
    }

    /**
     * @param $sku
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductImage($sku)
    {
        $imageUrl = null;
        if (!empty($sku)) {
            $product = $this->productRepository->get($sku);
            $imageUrl = $this->_imageHelper->init($product, 'product_base_image')->getUrl();
        } else {
            $imageUrl = $this->_imageHelper->init($this->getProduct(), 'product_base_image')->getUrl();
        }
        return $imageUrl;
    }
}
