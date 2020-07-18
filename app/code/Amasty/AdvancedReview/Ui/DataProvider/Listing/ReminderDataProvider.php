<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Ui\DataProvider\Listing;

use Amasty\AdvancedReview\Model\ResourceModel\Reminder\Grid\Collection;
use Amasty\AdvancedReview\Api\ReminderRepositoryInterface;

/**
 * Class ReminderDataProvider
 * @package Amasty\AdvancedReview\Ui\DataProvider\Listing
 */
class ReminderDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const SEC_IN_DAY = 86400;

    /**
     * @var ReminderRepositoryInterface
     */
    private $repository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        ReminderRepositoryInterface $repository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->repository = $repository;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as $key => $reminder) {
            if (isset($reminder['name'])) {
                $reminder['name'] = rtrim($reminder['name'], ", ");
            }

            $data['items'][$key] = $reminder;
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getPrimaryFieldName()
    {
        return 'entity_id';
    }
}
