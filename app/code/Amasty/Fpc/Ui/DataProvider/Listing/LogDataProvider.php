<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Ui\DataProvider\Listing;

class LogDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Amasty\Fpc\Model\ResourceModel\Log\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\Fpc\Model\ResourceModel\Log\CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collectionFactory = $collectionFactory;
    }

    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
        }

        return $this->collection;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() === "load_time") {
            $filter->setValue(trim($filter->getValue(), "%"));
        }

        parent::addFilter($filter);
    }
}
