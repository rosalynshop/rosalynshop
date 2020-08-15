<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report
 * @version   1.3.44
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Ui\Component\Toolbar\Filter;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;
use Mirasvit\Report\Api\Service\DateServiceInterface;
use Magento\Framework\Stdlib\DateTime;
use Mirasvit\Report\Model\Config;

class Date extends AbstractComponent
{
    /**
     * @var DateServiceInterface
     */
    private $dateService;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $columnName;

    public function __construct(
        DateServiceInterface $dateService,
        Config $config,
        $column,
        ContextInterface $context,
        $components = [],
        array $data = []
    ) {
        $this->dateService = $dateService;
        $this->config = $config;
        $this->columnName = $column;

        parent::__construct($context, $components, $data);
    }

    public function getComponentName()
    {
        return 'toolbar_filter_date';
    }

    public function prepare()
    {
        $config = $this->getData('config');

        $intervals = [];

        foreach ($this->dateService->getIntervals() as $code => $label) {
            $interval = $this->dateService->getInterval($code, true);
            $intervals[$label] = [
                $interval->getFrom()->get(DateTime::DATETIME_INTERNAL_FORMAT),
                $interval->getTo()->get(DateTime::DATETIME_INTERNAL_FORMAT),
            ];
        }

        $config = array_merge_recursive($config, [
            'column'    => $this->columnName,
            'value'     => $this->getDefaultValue(),
            'intervals' => $intervals,
            'locale'    => $this->config->getLocaleData(),
        ]);

        $this->setData('config', $config);
    }

    /**
     * Get default date value for toolbar date filter.
     *
     * If date filter exist in GET params use them, otherwise - this month' dates.
     *
     * @return string[]
     */
    private function getDefaultValue()
    {
        return $this->context->getFilterParam($this->columnName, [
            'from' => $this->dateService->getInterval('month')->getFrom()->get(DateTime::DATE_INTERNAL_FORMAT),
            'to'   => $this->dateService->getInterval('month')->getTo()->get(DateTime::DATE_INTERNAL_FORMAT)
        ]);
    }
}
