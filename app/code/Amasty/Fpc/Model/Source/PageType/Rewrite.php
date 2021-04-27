<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\App\Emulation;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;

abstract class Rewrite extends Emulated
{
    /**
     * @var UrlRewriteCollectionFactory
     */
    private $rewriteCollectionFactory;

    protected $rewriteType;

    public function __construct(
        UrlRewriteCollectionFactory $rewriteCollectionFactory,
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
        $this->rewriteCollectionFactory = $rewriteCollectionFactory;
    }

    /**
     * @param $storeId
     *
     * @return UrlRewriteCollection
     */
    protected function getEntityCollection($storeId)
    {
        /** @var UrlRewriteCollection $rewriteCollection */
        $rewriteCollection = $this->rewriteCollectionFactory->create()
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('entity_type', $this->rewriteType);

        if ($storeId) {
            $rewriteCollection->addStoreFilter($storeId);
        }

        return $rewriteCollection;
    }

    /**
     * @param $entity
     * @param $storeId
     *
     * @return mixed
     */
    protected function getUrl($entity, $storeId)
    {
        return $entity->getData('request_path');
    }
}
