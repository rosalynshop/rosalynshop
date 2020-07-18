<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Review;

use Amasty\AdvancedReview\Model\Toolbar\Applier;
use Amasty\AdvancedReview\Model\Toolbar\UrlBuilder;
use Magento\Framework\View\Element\Template;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;

class Toolbar extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::review/toolbar.phtml';

    /**
     * Review collection
     *
     * @var ReviewCollection
     */
    private $collection;

    /**
     * @var \Amasty\AdvancedReview\Model\Sources\Sort
     */
    private $sortModel;

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var Applier
     */
    private $applier;

    /**
     * @var \Amasty\AdvancedReview\Model\Sources\Filter
     */
    private $filterModel;

    public function __construct(
        Template\Context $context,
        \Amasty\AdvancedReview\Model\Sources\Sort $sortModel,
        \Amasty\AdvancedReview\Model\Sources\Filter $filterModel,
        \Amasty\AdvancedReview\Helper\Config $config,
        \Magento\Framework\App\Request\Http $request,
        UrlBuilder $urlBuilder,
        Applier $applier,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sortModel = $sortModel;
        $this->config = $config;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->applier = $applier;
        $this->filterModel = $filterModel;
    }

    /**
     * @param $type
     * @param $value
     *
     * @return string
     */
    public function generateUrl($type, $value)
    {
        return $this->urlBuilder->generateUrl($type, $value);
    }

    /**
     * @return bool
     */
    public function isToolbarDisplayed()
    {
        return $this->isSortingEnabled() || $this->isFilteringEnabled();
    }

    /**
     * @return bool
     */
    public function isSortingEnabled()
    {
        return (bool)count($this->config->getSortingOptions());
    }

    /**
     * @return bool
     */
    public function isFilteringEnabled()
    {
        return (bool)count($this->config->getFilteringOptions());
    }

    /**
     * @return array
     */
    public function getAvailableOrders()
    {
        $available = $this->config->getSortingOptions();
        $data = $this->sortModel->toOptionArray();
        $result = [];
        foreach ($data as $key => $order) {
            if (in_array($order['value'], $available)) {
                $result[$order['value']] = $order['label'];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAvailableFilters()
    {
        $available = $this->config->getFilteringOptions();
        $data = $this->filterModel->toOptionArray();
        $result = [];
        foreach ($data as $key => $order) {
            if (in_array($order['value'], $available)) {
                $result[$order['value']] = $order['label'];
            }
        }

        return $result;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isOrderCurrent($key)
    {
        return $this->applier->isOrderCurrent($key, $this->getCollection());
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isFilterSelected($key)
    {
        return $this->applier->isFilterSelected($key, $this->getCollection());
    }

    /**
     * @return string
     */
    public function getCurrentDirection()
    {
        $direction = $this->getRequest()->getParam(UrlBuilder::DIRECTION_PARAM_NAME, 'DESC');
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'DESC';
        $direction = strtoupper($direction);
        return $direction;
    }

    /**
     * @return ReviewCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param ReviewCollection $collection
     *
     * @return $this
     */
    public function setCollection(ReviewCollection $collection)
    {
        $this->collection = $collection;

        return $this;
    }
}
