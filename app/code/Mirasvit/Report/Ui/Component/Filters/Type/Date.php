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



namespace Mirasvit\Report\Ui\Component\Filters\Type;

use Magento\Framework\Stdlib\DateTime;

class Date extends \Magento\Ui\Component\Filters\Type\Date
{
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];

            if (isset($value['from'])) {
                $this->applyFilterByType('gteq', $this->convertDate(
                    $value['from'],
                    0,
                    0,
                    0
                ), 'A');
            }

            if (isset($value['to'])) {
                $this->applyFilterByType('lteq', $this->convertDate(
                    $value['to'],
                    23,
                    59,
                    59
                ), 'A');
            }

            if (isset($value['comparisonEnabled']) && $value['comparisonEnabled'] !== 'false') {
                if (isset($value['compareFrom'])) {
                    $this->applyFilterByType('gteq', $this->convertDate(
                        $value['compareFrom'],
                        0,
                        0,
                        0
                    ), 'c');
                }

                if (isset($value['compareTo'])) {
                    $this->applyFilterByType('lteq', $this->convertDate(
                        $value['compareTo'],
                        23,
                        59,
                        59
                    ), 'c');
                }
            }
        }
    }

    private function convertDate($date, $hour, $minute, $second)
    {
        $d = new \DateTime($date);
        $d->setTime($hour, $minute, $second);

        return $d;
    }

    /**
     * Apply filter by its type
     *
     * @param string $type
     * @param \DateTime $value
     * @param string $group
     * @return void
     */
    protected function applyFilterByType($type, $value, $group = '')
    {
        if (!empty($value)) {
            $filter = $this->filterBuilder->setConditionType($type)
                ->setField($this->getName())
                ->setValue($value->format(DateTime::DATETIME_PHP_FORMAT))
                ->create();

            $this->getContext()->getDataProvider()->addFilter($filter, $group);
        }
    }
}