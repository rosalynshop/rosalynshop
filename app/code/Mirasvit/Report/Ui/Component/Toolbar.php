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



namespace Mirasvit\Report\Ui\Component;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextFactory;
use Magento\Ui\Component\AbstractComponent;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Mirasvit\Report\Ui\Context;
use Mirasvit\ReportApi\Api\SchemaInterface;

class Toolbar extends AbstractComponent
{
    /**
     * @var Context
     */
    private $uiContext;

    /**
     * @var SchemaInterface
     */
    private $provider;

    private $componentFactory;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var ContextFactory
     */
    private $contextFactory;

    public function __construct(
        ContextFactory $contextFactory,
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata,
        UiComponentFactory $componentFactory,
        SchemaInterface $provider,
        ContextInterface $context,
        Context $uiContext,
        array $components = [],
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
        $this->componentFactory = $componentFactory;
        $this->provider = $provider;
        $this->uiContext = $uiContext;

        parent::__construct($context, $components, $data);
        $this->contextFactory = $contextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getComponentName()
    {
        return 'toolbar';
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function prepare()
    {
        $this->prepareOptions();

        $dimension = $this->uiContext->getActiveDimension();

        $this->getDataProvider()
            ->setDimension($dimension);

        parent::prepare();
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareOptions()
    {
        $config = $this->getData('config');

        $config['fastFilters'] = [];
        foreach ($this->uiContext->getReport()->getFastFilters() as $columnName) {
            $type = $this->provider->getColumn($columnName)->getType()->getType();

            $component = $this->createComponent($type, $columnName);

            $this->addComponent($columnName, $component);
        }

        $config['dimensions'] = [];
        foreach ($this->uiContext->getReport()->getDimensions() as $columnName) {
            $jsConfig = [];
            $jsConfig['column'] = $columnName;
            $jsConfig['label'] = $this->provider->getColumn($columnName)->getLabel();


            $config['dimensions'][] = $jsConfig;
        }

        $config['dimension'] = $this->uiContext->getActiveDimension();

        $this->setData('config', $config);
    }
    //
    //    /**
    //     * @param string $columnName
    //     * @return string
    //     */
    //    protected function getFilterValue($columnName)
    //    {
    //        $value = $this->getContext()->getFilterParam($columnName);
    //        if ($value) {
    //            $this->uiContext->getSession()->setData($columnName, $value);
    //        } else {
    //            $value = $this->uiContext->getSession()->getData($columnName);
    //        }
    //
    //        return $value;
    //    }
    //
    /**
     * @return \Mirasvit\Report\Ui\DataProvider
     */
    protected function getDataProvider()
    {
        return $this->getContext()->getDataProvider();
    }

    /**
     * Create toolbar component.
     *
     * @param string $type       - column type
     * @param string $columnName - column name
     *
     * @return \Magento\Framework\View\Element\UiComponentInterface
     * @throws LocalizedException
     */
    private function createComponent($type, $columnName)
    {
        $componentName = "toolbar_filter_$type";
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '<')) {
            $componentClass = 'Mirasvit\Report\Ui\Component\Toolbar\Filter\\' . ucfirst($type);
            if (!class_exists($componentClass)) {
                throw new LocalizedException(__(
                    'Invalid component type "%1", class %2 does not exists.',
                    $type,
                    $componentClass
                ));
            }

            $component = $this->objectManager->create($componentClass, [
                'column'  => $columnName,
                'context' => $this->contextFactory->create(['namespace' => $componentName]),
                'data'    => [
                    'name'   => $componentName,
                    'config' => [
                        'component' => 'Mirasvit_Report/js/toolbar/filter/' . $type,
                    ]
                ],
            ]);
        } else {
            $component = $this->componentFactory->create($componentName, null, [
                'column' => $columnName,
            ]);
        }

        /** @var \Mirasvit\Report\Ui\Component\Toolbar\Filter\Date $component */
        $component->prepare();

        return $component;
    }
}
