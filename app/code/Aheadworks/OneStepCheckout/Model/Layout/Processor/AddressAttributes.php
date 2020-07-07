<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMetaProvider;
use Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Layout\MultilineModifier;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes\Merger;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes\FieldRowsSorter;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Stdlib\ArrayManager;
use RosalynShop\RegionManager\Model\Config;
use RosalynShop\RegionManager\Model\Source\StateOptions;

/**
 * Class AddressAttributes
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class AddressAttributes implements LayoutProcessorInterface
{
    /**
     * @var AttributeMetaProvider
     */
    private $attributeMataProvider;

    /**
     * @var Merger
     */
    private $attributeMerger;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var FieldRowsSorter
     */
    private $rowsSorter;

    /**
     * @var MultilineModifier
     */
    private $multilineModifier;

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * @var StateOptions
     */
    protected $_stateOption;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @param AttributeMetaProvider $attributeMataProvider
     * @param Merger $attributeMerger
     * @param ArrayManager $arrayManager
     * @param FieldRowsSorter $rowsSorter
     * @param MultilineModifier $multilineModifier
     */
    public function __construct(
        AttributeMetaProvider $attributeMataProvider,
        Merger $attributeMerger,
        ArrayManager $arrayManager,
        FieldRowsSorter $rowsSorter,
        MultilineModifier $multilineModifier,
        StateOptions $stateOption,
        Config $config,
        DirectoryHelper $directoryHelper
    ) {
        $this->attributeMataProvider = $attributeMataProvider;
        $this->attributeMerger = $attributeMerger;
        $this->arrayManager = $arrayManager;
        $this->rowsSorter = $rowsSorter;
        $this->multilineModifier = $multilineModifier;
        $this->directoryHelper = $directoryHelper;
        $this->_stateOption = $stateOption;
        $this->_config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $shippingAddressFieldRowsPath = 'components/checkout/children/shippingAddress/children/shipping-address-fieldset/children';
        $shippingAddressFieldRowsLayout = $this->arrayManager->get($shippingAddressFieldRowsPath, $jsLayout);

        if ($shippingAddressFieldRowsLayout) {
            $shippingAddressFieldRowsLayout = $this->attributeMerger->merge(
                $this->attributeMataProvider->getMetadata('shipping'),
                'checkoutProvider',
                'shippingAddress',
                $shippingAddressFieldRowsLayout
            );
            $shippingAddressFieldRowsLayout = $this->rowsSorter->sort($shippingAddressFieldRowsLayout, 'shipping');
            $shippingAddressFieldRowsLayout = $this->multilineModifier->modify(
                $shippingAddressFieldRowsLayout,
                'shipping'
            );


            $jsLayout = $this->arrayManager->set(
                $shippingAddressFieldRowsPath,
                $jsLayout,
                $shippingAddressFieldRowsLayout
            );

            if (isset($jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country-region-zip-field-row']['children']['postcode'])) {
                unset($jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country-region-zip-field-row']['children']['postcode']);
            }

            $this->regionOptions($jsLayout);
            $this->cityOptions($jsLayout);
        }

        $billingAddressFieldRowsPath = 'components/checkout/children/paymentMethod/children/billingAddress/children/billing-address-fieldset/children';
        $billingAddressFieldRowsLayout = $this->arrayManager->get($billingAddressFieldRowsPath, $jsLayout);
        if ($billingAddressFieldRowsLayout) {
            $billingAddressFieldRowsLayout = $this->attributeMerger->merge(
                $this->attributeMataProvider->getMetadata('billing'),
                'checkoutProvider',
                'billingAddress',
                $billingAddressFieldRowsLayout
            );
            $billingAddressFieldRowsLayout = $this->rowsSorter->sort($billingAddressFieldRowsLayout, 'billing');
            $billingAddressFieldRowsLayout = $this->multilineModifier->modify(
                $billingAddressFieldRowsLayout,
                'billing'
            );
            $jsLayout = $this->arrayManager->set(
                $billingAddressFieldRowsPath,
                $jsLayout,
                $billingAddressFieldRowsLayout
            );
        }

        return $jsLayout;
    }

    /**
     * @param $jsLayout
     */
    private function regionOptions($jsLayout)
    {
        $regionOptions[] = [
            'label' => __('-- Please Select --'),
            'value' => ''
        ];
        foreach ($this->_stateOption->getStates() as $field) {
            $regionOptions[] = [
                'label' => $field['states_name'],
                'value' => $field['states_name']
            ];
        }

        $shippingAddressFieldSet = $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country-region-zip-field-row']['children'];
        $shippingAddressFieldSet['region_id'] = [];
        $shippingAddressFieldSet['region'] = [];
        $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country-region-zip-field-row']['children'] = $shippingAddressFieldSet;
        $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country-region-zip-field-row']['children']['region'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'drop-down',
                'additionalClasses' => 'state-drop-down',
            ],
            'dataScope' => 'shippingAddress.region',
            'label' => __('State/Province'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => ['required-entry' => true],
            'sortOrder' => '2',
            'id' => 'state-drop-down',
            'options' => $regionOptions
        ];

    }

    /**
     * @param $jsLayout
     */
    private function cityOptions($jsLayout)
    {
        $shippingAddressFieldSet = $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city-field-row']['children'];

        $shippingAddressFieldSet['city'] = [];
        $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city-field-row']['children'] = $shippingAddressFieldSet;
        $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city-field-row']['children']['city'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'drop-down',
                'additionalClasses' => 'city-drop-down',
            ],
            'dataScope' => 'shippingAddress.city',
            'label' => 'City',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => ['required-entry' => true],
            'sortOrder' => '3',
            'id' => 'city-drop-down',
            'options' => [
                [
                    'value' => '',
                    'label' => __('-- Please Select --')
                ],
            ]
        ];
    }
}
