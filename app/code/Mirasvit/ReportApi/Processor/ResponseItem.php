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

use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\TestModuleDefaultHydrator\Api\Data\ExtensionAttributeInterface;
use Mirasvit\ReportApi\Api\Processor\ResponseItemInterface;

class ResponseItem extends AbstractSimpleObject implements ResponseItemInterface
{
    const DATA = 'data';
    const FORMATTED_DATA = 'formatted_data';

    public function getData($key = null)
    {
        if ($key) {
            return isset($this->_get(self::DATA)[$key]) ? $this->_get(self::DATA)[$key] : null;
        }

        return $this->_get(self::DATA);
    }

    public function getFormattedData($key = null)
    {
        if ($key) {
            return isset($this->_get(self::FORMATTED_DATA)[$key]) ? $this->_get(self::FORMATTED_DATA)[$key] : null;
        }

        return $this->_get(self::FORMATTED_DATA);
    }
}
