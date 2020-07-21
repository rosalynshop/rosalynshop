<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Magento\Cms\Model\ResourceModel\Page\Collection as PageCollection;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Cms extends Emulated
{
    /**
     * @var PageCollection
     */
    private $pageCollection;

    public function __construct(
        PageCollectionFactory $pageCollectionFactory,
        UrlInterface $urlBuilder,
        Emulation $appEmulation,
        State $appState,
        StoreManagerInterface $storeManager,
        $isMultiStoreMode = false,
        array $stores = [],
        \Closure $filterCollection = null
    ) {
        parent::__construct(
            $urlBuilder,
            $appEmulation,
            $appState,
            $storeManager,
            $isMultiStoreMode,
            $stores,
            $filterCollection
        );
        $this->pageCollection = $pageCollectionFactory->create();
        $this->pageCollection->addFieldToFilter('is_active', true);
    }

    /**
     * @param $storeId
     *
     * @return PageCollection
     */
    protected function getEntityCollection($storeId)
    {
        return $this->pageCollection;
    }

    /**
     * @param $entity
     * @param $storeId
     *
     * @return bool|string
     */
    protected function getUrl($entity, $storeId)
    {
        if ($this->isMultiStoreMode
            && !in_array(Store::DEFAULT_STORE_ID, $entity->getStores())
            && !in_array($storeId, $entity->getStores())
        ) {
            // Page is not visible for this store
            return false;
        } else {
            return $entity->getIdentifier() . '/';
        }
    }
}
