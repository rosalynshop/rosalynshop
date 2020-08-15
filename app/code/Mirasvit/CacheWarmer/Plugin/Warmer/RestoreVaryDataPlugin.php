<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Plugin\Warmer;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\CacheWarmer\Api\Service\PageServiceInterface;
use Mirasvit\CacheWarmer\Api\Service\WarmerServiceInterface;

/**
 * Plugin for \Magento\Framework\App\FrontControllerInterface
 */
class RestoreVaryDataPlugin
{
    /**
     * @var WarmerServiceInterface
     */
    private $warmerService;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    public function __construct(
        WarmerServiceInterface $warmerService,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        CustomerCollectionFactory $customerCollectionFactory,
        Registry $registry
    ) {
        $this->warmerService             = $warmerService;
        $this->storeManager              = $storeManager;
        $this->customerSession           = $customerSession;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->registry                  = $registry;
    }

    /**
     * @param Magento\Framework\App\FrontControllerInterface $subject
     * @param Magento\Framework\App\Request\Http             $request
     * @return void
     */
    public function beforeDispatch($subject, $request)
    {
        $varyData = $this->warmerService->getVaryData();

        if ($varyData) {
            /** @var \Magento\Store\Model\Store $store */
            $store = $this->storeManager->getStore();

            if (isset($varyData['current_currency'])) {
                $store->setCurrentCurrencyCode($varyData['current_currency']);
            }

            if (isset($varyData['customer_group'])) {
                $customer = $this->customerCollectionFactory->create()
                    ->addFieldToFilter('group_id', $varyData['customer_group'])
                    ->getFirstItem();
                if ($customer) {
                    $this->customerSession->loginById($customer->getId());
                }
            }
        }

        if ($productId = $this->warmerService->getProductId()) {
            $this->registry->register(PageServiceInterface::PRODUCT_REG, $productId);

        }

        if ($categoryId = $this->warmerService->getCategoryId()) {
            $this->registry->register(PageServiceInterface::CATEGORY_REG, $categoryId);
        }
    }
}