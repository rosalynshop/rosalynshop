<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\Notification\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Zemi\Notification\Helper
 */
class Data extends \Zemi\Base\Helper\Data
{
    const VOUCHER = 'zmvoucher/voucher/show';
    const VOUCHER_DESCRIBE = 'zmvoucher/voucher/describe';


    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $productRepository, $imageHelper, $customerSession, $storeManager);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function voucherShop()
    {
        return $this->getConfig(self::VOUCHER, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function voucherDescribe()
    {
        return $this->getConfig(self::VOUCHER_DESCRIBE, $this->getStoreId());
    }
}
