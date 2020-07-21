<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Model\CountryFactory;
use Amasty\SeoRichData\Model\Source\Product\Offer;

class Config extends AbstractHelper
{
    /**
     * @var array
     */
    private $socialNetworks = [
        'facebook',
        'twitter',
        'google',
        'instagram',
        'youtube',
        'linkedin',
        'myspace',
        'pinterest',
        'soundcloud',
        'tumblr'
    ];

    private $organizationContacts = [
        'sales',
        'technical_support',
        'customer_service'
    ];

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    public function __construct(Context $context, CountryFactory $countryFactory)
    {
        parent::__construct($context);
        $this->countryFactory = $countryFactory;
    }

    /**
     * @param string $settingPath
     * @return mixed
     */
    private function getConfigValue($settingPath)
    {
        $value = $this->scopeConfig->getValue(
            'amseorichdata/' . $settingPath,
            ScopeInterface::SCOPE_STORE
        );

        return $value;
    }

    /**
     * @return mixed
     */
    public function forProductEnabled()
    {
        return $this->getConfigValue('product/enabled');
    }

    /**
     * @return mixed
     */
    public function getProductDescriptionMode()
    {
        return $this->getConfigValue('product/description');
    }

    /**
     * @return mixed
     */
    public function showAsList($type)
    {
        return $this->getConfigValue('product/' . $type);
    }

    /**
     * @param $type
     *
     * @return bool
     */
    public function showAggregate($type)
    {
        return $this->getConfigValue('product/' . $type) == Offer::AGGREGATE;
    }

    /**
     * @return mixed
     */
    public function showAvailability()
    {
        return $this->getConfigValue('product/availability');
    }

    /**
     * @return mixed
     */
    public function showCondition()
    {
        return $this->getConfigValue('product/condition');
    }

    /**
     * @return mixed
     */
    public function showRating()
    {
        return $this->getConfigValue('product/rating');
    }

    /**
     * @return mixed
     */
    public function isBreadcrumbsEnabled()
    {
        return $this->getConfigValue('breadcrumbs/enabled');
    }

    /**
     * @return mixed
     */
    public function forWebsiteEnabled()
    {
        return $this->getConfigValue('website/enabled');
    }

    /**
     * @return mixed
     */
    public function getWebsiteName()
    {
        return $this->getConfigValue('website/name');
    }

    /**
     * @return mixed
     */
    public function forOrganizationEnabled()
    {
        return $this->getConfigValue('organization/enabled');
    }

    /**
     * @return mixed
     */
    public function getOrganizationName()
    {
        return $this->getConfigValue('organization/name');
    }

    /**
     * @return mixed
     */
    public function getOrganizationLogo()
    {
        return $this->getConfigValue('organization/logo_url');
    }

    /**
     * @return mixed
     */
    public function forCategoryEnabled()
    {
        return $this->getConfigValue('category/enabled');
    }

    /**
     * @return mixed
     */
    public function forSearchEnabled()
    {
        return $this->getConfigValue('search/enabled');
    }

    /**
     * Getting all social links
     *
     * @return array
     */
    public function getSocialLinks()
    {
        $links = [];
        foreach ($this->socialNetworks as $socialNetwork) {
            if ($link = trim($this->getConfigValue('social/' . $socialNetwork))) {
                $links[] = $link;
            }
        }

        return $links;
    }

    /**
     * @return mixed
     */
    public function forSocialEnabled()
    {
        return $this->getConfigValue('social/enabled');
    }

    /**
     * @return mixed
     */
    public function getOrganizationDescription()
    {
        $description = $this->getConfigValue('organization/description');
        if ($length = $this->getOrganizationDescLength()) {
            $description = substr($description, 0, $length);
            $description .= '...';
        }

        return $description;
    }

    /**
     * @return int
     */
    public function getOrganizationDescLength()
    {
        return (int)$this->getConfigValue('organization/description_length');
    }

    /**
     * @return mixed
     */
    public function getOrganizationLocation()
    {
        return $this->getConfigValue('organization/location');
    }

    /**
     * Getting all organization contacts
     *
     * @return array
     */
    public function getOrganizationContacts()
    {
        $contacts = [];
        foreach ($this->organizationContacts as $organizationContact) {
            if ($contact = trim($this->getConfigValue('organization/' . $organizationContact))) {
                $contacts[$organizationContact] = $contact;
            }
        }

        return $contacts;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        $result = null;
        if ($countryCode = $this->getConfigValue('organization/country')) {
            $result = $this->countryFactory->create()
                ->loadByCode($countryCode)
                ->getName();
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->getConfigValue('organization/postal_code');
    }

    /**
     * @return mixed
     */
    public function getOrganizationRegion()
    {
        return $this->getConfigValue('organization/region');
    }

    /**
     * @return mixed
     */
    public function getOrganizationCity()
    {
        return $this->getConfigValue('organization/city');
    }

    /**
     * @return mixed
     */
    public function sliceBreadcrumbs()
    {
        return $this->getConfigValue('breadcrumbs/type');
    }

    /**
     * @return mixed
     */
    public function getBrandAttribute()
    {
        return $this->getConfigValue('product/brand');
    }

    /**
     * @return mixed
     */
    public function getManufacturerAttribute()
    {
        return $this->getConfigValue('product/manufacturer');
    }

    /**
     * @return array
     */
    public function getCustomAttributes()
    {
        $attributes = array_map(
            function ($pair) {
                return array_filter(explode(',', $pair));
            }, array_filter(explode(
                "\n",
                $this->getConfigValue('product/custom_properties')
            ))
        );

        return $attributes;
    }
}
