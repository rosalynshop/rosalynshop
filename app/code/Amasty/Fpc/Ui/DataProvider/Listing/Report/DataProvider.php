<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Ui\DataProvider\Listing\Report;

use Amasty\Fpc\Model\ResourceModel\Reports\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Amasty\Fpc\Model\ResourceModel\Reports\Collection;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create()->prepareCollection($request->getParam('grid_type', Collection::DATE_TYPE_DAY));
        $this->collectionFactory = $collectionFactory;
    }

    public function getData()
    {
        $result = parent::getData();
        $this->addPersentColumns($result);

        return $result;
    }

    /**
     * @param $result
     */
    private function addPersentColumns(&$result)
    {
        $result['totals'] = [];
        $fields = [
            'response_time', 'hit_response_time', 'miss_response_time', 'hits', 'misses', 'visits'
        ];
        foreach ($result['items'] as &$item) {
            foreach ($fields as $field) {
                if (empty($result['totals'][$field])) {
                    $result['totals'][$field] = $item[$field];
                } else {
                    $result['totals'][$field] += $item[$field];
                }
            }
        }

        array_pop($fields);

        if(!empty($result['totals'])) {
            foreach ($fields as $field) {
                $result['totals'][$field] = round($result['totals'][$field] / $result['totalRecords'], 2);
            }
        }
    }

    /**
     * @param string $field
     * @param string $direction
     */
    public function addOrder($field, $direction)
    {
        if ($field === 'visited_at') {
            $field = 'dt';
        }

        parent::addOrder($field, $direction);
    }
}
