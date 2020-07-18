<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Toolbar;

use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Model\Sources\Filter;
use Amasty\AdvancedReview\Model\Sources\Sort as SortModel;

class Applier
{
    const COLLECTION_FLAG = 'amreview-filders-applied';

    const DEFAULT_DIR = 'DESC';

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var SortModel
     */
    private $sortModel;

    public function __construct(
        UrlBuilder $urlBuilder,
        SortModel $sortModel
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->sortModel = $sortModel;
    }

    /**
     * @param ReviewCollection $reviewCollection
     */
    public function execute(ReviewCollection $reviewCollection)
    {
        if ($reviewCollection->getFlag(self::COLLECTION_FLAG)) {
            return;
        }

        $applied = $this->urlBuilder->collectParams();
        if (!$applied) {
            return;
        }

        $reviewCollection->setFlag(self::COLLECTION_FLAG, true);
        krsort($applied);//sorting should be before dir
        foreach ($applied as $condition => $value) {
            switch ($condition) {
                case UrlBuilder::STARS_PARAM_NAME:
                    $this->applyStarFilter($reviewCollection, $value);
                    break;
                case UrlBuilder::DIRECTION_PARAM_NAME:
                    $this->applyDirection($reviewCollection, $value);
                    break;
                case UrlBuilder::SORT_PARAM_NAME:
                    $this->applySorting($reviewCollection, $value);
                    break;
                case Filter::WITH_IMAGES:
                case Filter::VERIFIED:
                case Filter::RECOMMENDED:
                    $this->applyFilter($reviewCollection, $condition, $value);
                    break;
            }
        }
    }

    /**
     * @param ReviewCollection $reviewCollection
     * @param $condition
     * @param $value
     */
    protected function applyFilter(ReviewCollection $reviewCollection, $condition, $value)
    {
        if ($value) {
            if ($condition == Filter::WITH_IMAGES) {
                $reviewCollection->setFlag(Filter::WITH_IMAGES, true);
            } else {
                $reviewCollection->addFilter($condition, $value);
            }
        }
    }

    /**
     * @param ReviewCollection $reviewCollection
     * @param $value
     */
    protected function applyStarFilter(ReviewCollection $reviewCollection, $value)
    {
        if ($value && !$reviewCollection->getFlag('filter_by_stars')) {
            $reviewCollection->setFlag('filter_by_stars', $value);
            $reviewCollection->getSelect()->having('rating_summary = ?', $value);
        }
    }

    /**
     * @param ReviewCollection $reviewCollection
     * @param $value
     */
    protected function applyDirection(ReviewCollection $reviewCollection, $value)
    {
        $value = strtoupper($value);
        if ($value && in_array($value, ['ASC', 'DESC'])) {
            $orders = $reviewCollection->getOrder();
            foreach ($orders as $order => $dir) {
                if ($dir != $value) {
                    $orders[$order] = $value;
                }
                break;
            }
            $reviewCollection->setOrders($orders);
        }
    }

    /**
     * @param ReviewCollection $reviewCollection
     * @param $value
     */
    protected function applySorting(ReviewCollection $reviewCollection, $value)
    {
        $availableOrders = $this->sortModel->toArray();
        if ($value && isset($availableOrders[$value])) {
            $value = $this->convertKeyToAlias($value);
            if ($value) {
                $this->applyAdditionalFlag($value, $reviewCollection);
                $orders = [$value => self::DEFAULT_DIR];
                if ($value !== SortModel::NEWEST_ALIAS) {
                    $orders = $orders + [SortModel::NEWEST_ALIAS => self::DEFAULT_DIR];
                }
                $reviewCollection->setOrders($orders);
            }
        }
    }

    /**
     * @param string $key
     * @param ReviewCollection $reviewCollection
     */
    public function isFilterSelected(string $key, ReviewCollection $reviewCollection)
    {
        $result = false;
        if ($key == Filter::WITH_IMAGES) {
            $result = $reviewCollection->getFlag(Filter::WITH_IMAGES);
        } else {
            $filters = $reviewCollection->getFilter([]);
            foreach ($filters as $filter) {
                if ($filter->getField() == $key) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $key
     * @param ReviewCollection $reviewCollection
     */
    public function isOrderCurrent(string $key, ReviewCollection $reviewCollection)
    {
        $key = $this->convertKeyToAlias($key);
        $current = $this->getCurrentOrder($reviewCollection);

        return $key === $current;
    }

    /**
     * @param ReviewCollection $reviewCollection
     *
     * @return mixed
     */
    protected function getCurrentOrder(ReviewCollection $reviewCollection)
    {
        $orders = $reviewCollection->getOrder();
        foreach ($orders as $key => $order) {
            return $key;
        }

        return null;
    }

    /**
     * @param $key
     *
     * @return string|null
     */
    protected function convertKeyToAlias($key)
    {
        $result = null;
        switch ($key) {
            case SortModel::NEWEST:
                $result = SortModel::NEWEST_ALIAS;
                break;
            case SortModel::TOP_RATED:
                $result = SortModel::TOP_RATED_ALIAS;
                break;
            case SortModel::HELPFUL:
                $result = SortModel::HELPFUL_ALIAS;
                break;
        }

        return $result;
    }

    /**
     * @param $value
     * @param ReviewCollection $reviewCollection
     *
     * @return $this
     */
    protected function applyAdditionalFlag($value, ReviewCollection $reviewCollection)
    {
        $reviewCollection->setFlag('join_' . $value, true);
        return $this;
    }
}
