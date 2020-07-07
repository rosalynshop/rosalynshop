<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Plugin\Model\Address;

use Magento\Eav\Model\Config;
use Magento\Directory\Helper\Data;

/**
 * Class CustomAbstractAddress
 * @package RosalynShop\RegionManager\Plugin\Model\Address
 */
class CustomAbstractAddress
{
    /**
     * Directory data
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryData = null;

    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * CustomAbstractAddress constructor.
     * @param Data $directoryData
     * @param Config $eavConfig
     */
    public function __construct(
        Data $directoryData,
        Config $eavConfig
    ) {
        $this->_directoryData   = $directoryData;
        $this->_eavConfig       = $eavConfig;
    }

    /**
     * @param \Magento\Customer\Model\Address\AbstractAddress $subject
     * @param callable $proceed
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function aroundValidate(\Magento\Customer\Model\Address\AbstractAddress $subject, callable $proceed)
    {
        $errors = [];
        if (!\Zend_Validate::is($subject->getFirstname(), 'NotEmpty')) {
            $errors[] = __('%fieldName is a required field.', ['fieldName' => 'firstname']);
        }
        if (!\Zend_Validate::is($subject->getLastname(), 'NotEmpty')) {
            $errors[] = __('%fieldName is a required field.', ['fieldName' => 'lastname']);
        }
        if (!\Zend_Validate::is($subject->getStreetLine(1), 'NotEmpty')) {
            $errors[] = __('%fieldName is a required field.', ['fieldName' => 'street']);
        }
        if (!\Zend_Validate::is($subject->getCity(), 'NotEmpty')) {
            $errors[] = __('%fieldName is a required field.', ['fieldName' => 'city']);
        }
        if ($this->isTelephoneRequired()) {
            if (!\Zend_Validate::is($subject->getTelephone(), 'NotEmpty')) {
                $errors[] = __('%fieldName is a required field.', ['fieldName' => 'telephone']);
            }
        }
        if ($this->isFaxRequired()) {
            if (!\Zend_Validate::is($subject->getFax(), 'NotEmpty')) {
                $errors[] = __('%fieldName is a required field.', ['fieldName' => 'fax']);
            }
        }
//        if ($this->isCompanyRequired()) {
//            if (!\Zend_Validate::is($subject->getCompany(), 'NotEmpty')) {
//                $errors[] = __('%fieldName is a required field.', ['fieldName' => 'company']);
//            }
//        }
//        $_havingOptionalZip = $this->_directoryData->getCountriesWithOptionalZip();
//        if (!in_array(
//                $subject->getCountryId(),
//                $_havingOptionalZip
//            ) && !\Zend_Validate::is(
//                $subject->getPostcode(),
//                'NotEmpty'
//            )
//        ) {
//            $errors[] = __('%fieldName is a required field.', ['fieldName' => 'postcode']);
//        }
        $countryId = $subject->getCountryId();
        if (!\Zend_Validate::is($countryId, 'NotEmpty')) {
            $errors[] = __('%fieldName is a required field.', ['fieldName' => 'countryId']);
        } else {
            //Checking if such country exists.
            if (!in_array($countryId, $this->_directoryData->getCountryCollection()->getAllIds(), true)) {
                $errors[] = __(
                    'Invalid value of "%value" provided for the %fieldName field.',
                    [
                        'fieldName' => 'countryId',
                        'value' => htmlspecialchars($countryId)
                    ]
                );
            }
        }
        if (empty($errors) || $subject->getShouldIgnoreValidation()) {
            return true;
        }
        return $errors;
    }

    /**
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isTelephoneRequired()
    {
        return ($this->_eavConfig->getAttribute('customer_address', 'telephone')->getIsRequired());
    }

    /**
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isFaxRequired()
    {
        return ($this->_eavConfig->getAttribute('customer_address', 'fax')->getIsRequired());
    }

    /**
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isCompanyRequired()
    {
        return ($this->_eavConfig->getAttribute('customer_address', 'company')->getIsRequired());
    }
}