<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Ui\DataProvider\Listing;

use Magento\Framework\DB\Select;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Api\Filter;
use Amasty\AdminActionsLog\Model\ResourceModel\Log\CollectionFactory;
use Amasty\AdminActionsLog\Model\ResourceModel\Log\Collection;
use Amasty\AdminActionsLog\Ui\Component\Listing\Filter\AddFullnameFilterToCollection;

class ActionsDataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    public function __construct(
        CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $fromPart = $this->collection->getSelect()->getPart(Select::FROM);
        if (isset($fromPart[AddFullnameFilterToCollection::TABLE_ALIAS])) {
            return parent::getData();
        }
        $adminTable = $this->collection->getTable('admin_user');
        $this->collection->getSelect()
            ->joinLeft(
                [AddFullnameFilterToCollection::TABLE_ALIAS => $adminTable],
                'main_table.username = ' . AddFullnameFilterToCollection::TABLE_ALIAS . '.username',
                ['fullname' => AddFullnameFilterToCollection::SQL_EXPRESSION, 'firstname', 'lastname']
            )
        ;

        return parent::getData();
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}
