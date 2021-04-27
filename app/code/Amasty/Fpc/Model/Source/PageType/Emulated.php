<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

abstract class Emulated extends AbstractPage
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var \Closure
     */
    private $filterCollection;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        UrlInterface $urlBuilder,
        Emulation $appEmulation,
        State $appState,
        StoreManagerInterface $storeManager,
        $isMultiStoreMode = false,
        array $stores = [],
        \Closure $filterCollection = null
    ) {
        parent::__construct($isMultiStoreMode, $stores);
        $this->urlBuilder = $urlBuilder;
        $this->appEmulation = $appEmulation;
        $this->filterCollection = $filterCollection;
        $this->appState = $appState;
        $this->storeManager = $storeManager;
    }

    abstract protected function getEntityCollection($storeId);
    abstract protected function getUrl($entity, $storeId);

    /**
     * @param int $limit
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function getAllPages($limit = 0)
    {
        $result = $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$this, 'getEmulatedPages'],
            [$limit]
        );

        return $result;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getEmulatedPages($limit = 0)
    {
        $result = [];
        $object = new \stdClass();

        foreach ($this->stores as $storeId) {
            try {
                $this->urlBuilder->setScope($this->storeManager->getStore($storeId));
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $collection = $this->getEntityCollection($storeId);

            if (is_callable($this->filterCollection)) {
                $callback = $this->filterCollection;
                $callback($collection);
            }

            foreach ($collection as $entity) {
                $url = $this->getUrl($entity, $storeId);
                $result [] = [
                    'url' => $this->urlBuilder->getUrl(null, [
                        '_nosid' => true,
                        'object' => $object, // Pass object to params to prevent url caching
                        '_direct' => $url
                    ]),
                    'store' => $storeId
                ];

                if (--$limit == 0) {
                    break 2;
                }
            }
        }

        return $result;
    }
}
