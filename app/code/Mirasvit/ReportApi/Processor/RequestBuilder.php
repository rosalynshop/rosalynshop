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
 * @package   mirasvit/module-report-api
 * @version   1.0.12
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Processor;

use Magento\Framework\Webapi\ServiceInputProcessor;
use Mirasvit\ReportApi\Api\RequestBuilderInterface;
use Mirasvit\ReportApi\Api\RequestInterface;
use Mirasvit\ReportApi\Api\RequestInterfaceFactory;

class RequestBuilder implements RequestBuilderInterface
{
    private $requestFactory;

    public function __construct(
        RequestInterfaceFactory $requestFactory,
        ServiceInputProcessor $serviceInputProcessor
    ) {
        $this->requestFactory = $requestFactory;
        $this->serviceInputProcessor = $serviceInputProcessor;
    }

    /**
     * @return RequestInterface
     */
    public function create()
    {
        return $this->requestFactory->create();
    }

    private function merge(RequestInterface $default, RequestInterface $extender)
    {
        foreach ($extender->getColumns() as $column) {
            $default->addColumn($column);
        }
        foreach ($extender->getFilters() as $filter) {
            $isReplaced = false;
            foreach ($default->getFilters() as $f) {
                if ($f->getColumn() == $filter->getColumn()) {
                    $f->setValue($filter->getValue());
                    $f->setConditionType($filter->getConditionType());
                    $isReplaced = true;
                }
            }
            if (!$isReplaced) {
                $default->addFilter($filter->getColumn(), $filter->getValue(), $filter->getConditionType());
            }
        }

        return $default;
    }
}
