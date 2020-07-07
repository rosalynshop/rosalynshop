<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use RosalynShop\RegionManager\Model\Source\StateOptions;
use RosalynShop\RegionManager\Model\Source\CityOptions;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Json\EncoderInterface;

class Data extends AbstractHelper
{
    /**
     * @var StateOptions
     */
    protected $_stateOption;

    /**
     * @var CityOptions
     */
    protected $_citiesOption;

    /**
     * @var FrontNameResolver
     */
    protected $frontNameResolver;

    /**
     * @var EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * Data constructor.
     * @param Context $context
     * @param StateOptions $stateOption
     * @param CityOptions $citiesOption
     * @param FrontNameResolver $frontNameResolver
     * @param EncoderInterface $jsonEncoder
     */
    public function __construct(
        Context $context,
        StateOptions $stateOption,
        CityOptions $citiesOption,
        FrontNameResolver $frontNameResolver,
        EncoderInterface $jsonEncoder
    ) {
        $this->_stateOption = $stateOption;
        $this->_citiesOption = $citiesOption;
        $this->frontNameResolver = $frontNameResolver;
        $this->_jsonEncoder = $jsonEncoder;
        parent::__construct($context);
    }

    public function getRegionValueStates()
    {
        $regionOptions = [];
        $regionOptions[0] = __('-- Please select --');
        foreach ($this->_stateOption->getStates() as $field) {
            $regionOptions[] = $field['states_name'];
        }
        return $regionOptions;
    }

    public function getCitiesValueStates()
    {
        $regionOptions = [];
        $regionOptions[0] = __('-- Please select --');
        foreach ($this->_citiesOption->getCities() as $field) {
            $regionOptions[] = $field['cities_name'];
        }
        return $regionOptions;
    }

    /**
     * @return string
     */
    public function getWardsAjaxUrl()
    {
        $frontName = $this->frontNameResolver->getFrontName(false);
        $options = [
            'wardsAjax' => [
                'wardsAjaxUrl' => $this->_getUrl($frontName . '/regionmanager/wards/wardsjs/key/')
            ]
        ];
        return $this->_jsonEncoder->encode($options);
    }
}
