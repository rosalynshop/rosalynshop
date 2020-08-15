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


namespace Mirasvit\Report\Ui\Report\Settings;

use Magento\Ui\Component\Form as UiForm;

class Form extends UiForm
{
    const DEFAULT_REPORT = 'order_overview';

    /**
     * {@inheritdoc}
     */
    public function getDataSourceData()
    {
        $dataSource = [];

        $id = strtolower($this->getContext()->getRequestParam(
            $this->getContext()->getDataProvider()->getRequestFieldName(),
            self::DEFAULT_REPORT
        ));

        $data = $this->getContext()->getDataProvider()->getData();

        if (isset($data[$id])) {
            $dataSource = [
                'data' => $data[$id]
            ];
        }

        return $dataSource;
    }
}
