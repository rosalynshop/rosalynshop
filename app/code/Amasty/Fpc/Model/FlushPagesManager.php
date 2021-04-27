<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Amasty\Fpc\Model\FlushPages;
use Amasty\Fpc\Model\FlushPagesFactory;
use Amasty\Fpc\Model\ResourceModel\FlushPages as FlushPagesResource;
use Amasty\Fpc\Model\ResourceModel\FlushPages\Collection;
use Amasty\Fpc\Model\ResourceModel\FlushPages\CollectionFactory;
use Amasty\Fpc\Model\Log;
use Amasty\Fpc\Model\ResourceModel\Log as LogResource;

class FlushPagesManager
{
    /**
     * @var FlushPagesResource
     */
    private $flushPagesResource;

    /**
     * @var CollectionFactory
     */
    private $flushPagesCollectionFactory;

    /**
     * @var LogResource
     */
    private $logResource;

    /**
     * @var \Amasty\Fpc\Model\FlushPagesFactory
     */
    private $flushPagesFactory;

    public function __construct(
        FlushPagesResource $flushPagesResource,
        FlushPagesFactory $flushPagesFactory,
        CollectionFactory $flushPagesCollectionFactory,
        LogResource $logResource
    ) {
        $this->flushPagesResource = $flushPagesResource;
        $this->flushPagesCollectionFactory = $flushPagesCollectionFactory;
        $this->logResource = $logResource;
        $this->flushPagesFactory = $flushPagesFactory;
    }

    /**
     * @param Log $logModel
     */
    public function addPageToFlush($logModel)
    {
        /** @var FlushPages $model */
        $model = $this->flushPagesFactory->create();
        $model->addData(['url' => $logModel->getData('url')]);
        $this->flushPagesResource->save($model);

        $this->logResource->delete($logModel);
    }

    /**
     * @param string $url
     *
     * @return bool|FlushPages
     */
    public function findPageToFlush($url)
    {
        /** @var Collection $collection */
        $collection = $this->flushPagesCollectionFactory->create();

        /** @var FlushPages $item */
        $item = $collection->addFieldToFilter('url', $url)->setPageSize(1)->getFirstItem();

        if ($item->getData()) {
            return $item;
        }

        return false;
    }

    /**
     * @param FlushPages $model
     */
    public function deletePageToFlush($model)
    {
        $this->flushPagesResource->delete($model);
    }
}
