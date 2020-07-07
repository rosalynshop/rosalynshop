<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use RosalynShop\RegionManager\Model\Config;
use RosalynShop\RegionManager\Model\Source\StateOptions;

/**
 * Class LayoutProcessor
 * @package RosalynShop\RegionManager\Block\Checkout
 */
class LayoutProcessor implements LayoutProcessorInterface
{
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
     * LayoutProcessor constructor.
     * @param StateOptions $stateOption
     * @param Config $config
     * @param DirectoryHelper $directoryHelper
     */
    public function __construct(
        StateOptions $stateOption,
        Config $config,
        DirectoryHelper $directoryHelper
    ) {
        $this->directoryHelper = $directoryHelper;
        $this->_stateOption = $stateOption;
        $this->_config = $config;
    }

    /**
     * @param array $result
     * @return array
     */
    public function process($result)
    {
        if ($this->_config->getEnableExtensionYesNo()) {
            if ($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']) {
                $regionOptions[] = ['label' => '-- Vui lòng chọn --', 'value' => ''];
                foreach ($this->_stateOption->getStates() as $field) {
                    $regionOptions[] = ['label' => $field['states_name'], 'value' => $field['states_name']];
                }

                $shippingAddressFieldSet['region'] = '';
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region'] = [
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
                    'sortOrder' => 75,
                    'id' => 'state-drop-down',
                    'options' => $regionOptions
                ];

                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = [
                    'component' => 'Magento_Ui/js/form/element/select',
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/select',
                        'id' => 'drop-down',
                        'additionalClasses' => 'city-drop-down',
                    ],
                    'dataScope' => 'shippingAddress.city',
                    'label' => __('City'),
                    'provider' => 'checkoutProvider',
                    'visible' => true,
                    'validation' => ['required-entry' => true],
                    'sortOrder' => 80,
                    'id' => 'city-drop-down',
                    'options' => [
                        [
                            'value' => '',
                            'label' => 'Chọn tỉnh/thành trước',
                        ]
                    ]
                ];
//                $result['components']['checkout']['children']['steps']
//                ['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode'] = [
//                    'component' => 'Magento_Ui/js/form/element/select',
//                    'config' => [
//                        'customScope' => 'shippingAddress',
//                        'template' => 'ui/form/field',
//                        'elementTmpl' => 'ui/form/element/select',
//                        'id' => 'drop-down',
//                        'additionalClasses' => 'postcode-drop-down',
//                    ],
//                    'dataScope' => 'shippingAddress.postcode',
//                    'label' => 'Zip/Postal Code ',
//                    'provider' => 'checkoutProvider',
//                    'visible' => true,
//                    'validation' => ['required-entry' => true],
//                    'sortOrder' => 85,
//                    'id' => 'postcode-drop-down',
//                    'options' => [
//                        [
//                            'value' => '',
//                            'label' => '-- Vui lòng chọn --',
//                        ]
//                    ]
//                ];
            }
        }
        return $result;
    }
}
