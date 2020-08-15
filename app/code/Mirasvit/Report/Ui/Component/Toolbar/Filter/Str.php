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
use Mirasvit\ReportApi\Api\SchemaInterface;

class Str extends AbstractComponent
{
    /**
     * @var string
     */
    private $column;

    /**
     * @var SchemaInterface
     */
    private $provider;

    public function __construct(
        SchemaInterface $schema,
        $column = '',
        ContextInterface $context,
        $components = [],
        array $data = []
    ) {
        $this->column = $column;
        $this->provider = $schema;

        parent::__construct($context, $components, $data);
    }

    public function getComponentName()
    {
        return 'toolbar_filter_str';
    }

    public function prepare()
    {
        $column = $this->provider->getColumn($this->column);
        $config = $this->getData('config');

        $config['column']  = $this->column;
        $config['label']  = ucfirst($column->getName());

        $this->setData('config', $config);
    }
}