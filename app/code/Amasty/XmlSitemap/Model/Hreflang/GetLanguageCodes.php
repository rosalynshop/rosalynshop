<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

use Amasty\XmlSitemap\Model\Source\Hreflang\Scope as ScopeSource;
use Amasty\XmlSitemap\Model\Source\Hreflang\Country as CountrySource;
use Amasty\XmlSitemap\Model\Source\Hreflang\Language as LanguageSource;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Store\Model\ScopeInterface;

class GetLanguageCodes implements GetLanguageCodesInterface
{
    const CODE_XDEFAULT = 'x-default';
    const XML_PATH_SCOPE = 'amxmlsitemap/hreflang/scope';
    const XML_PATH_LANGUAGE = 'amxmlsitemap/hreflang/language';
    const XML_PATH_COUNTRY = 'amxmlsitemap/hreflang/country';
    const XML_PATH_X_DEFAULT = 'amxmlsitemap/hreflang/x_default';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function execute($currentStoreId)
    {
        $languageCodes = [];
        $storeIds = $this->getStoreIds($currentStoreId);
        $languages = $this->getLanguagesByStoreIds($storeIds);
        $countryCodes = $this->getCountriesByStoreIds($storeIds);
        $xdefaultStoreId = $this->getXdefaultStoreId($currentStoreId);
        foreach ($languages as $storeId => $language) {
            if ($storeId == $xdefaultStoreId) {
                $language = self::CODE_XDEFAULT;
            } elseif (isset($countryCodes[$storeId])) {
                $language .= '-' . $countryCodes[$storeId];
            }

            $languageCodes[$storeId] = $language;
        }

        return $languageCodes;
    }


    /**
     * @param int $currentStoreId
     * @return array
     */
    private function getStoreIds($currentStoreId)
    {
        $storeIds = [];
        $stores = $this->isScopeGlobal()
            ? $this->storeManager->getStores()
            : $this->storeManager->getStore($currentStoreId)->getWebsite()->getStores();

        foreach ($stores as $storeId => $store) {
            if ($store->getIsActive()) {
                $storeIds[] = $storeId;
            }
        }

        return $storeIds;
    }

    /**
     * @param $currentStoreId
     * @return null|string
     */
    protected function getXdefaultStoreId($currentStoreId)
    {
        $websiteId = $this->isScopeGlobal() ? 0 : $this->storeManager->getStore($currentStoreId)->getWebsiteId();
        return $this->scopeConfig
            ->getValue(self::XML_PATH_X_DEFAULT, ScopeInterface::SCOPE_WEBSITES, $websiteId);
    }

    /**
     * @param array $storeIds
     * @return array
     */
    private function getCountriesByStoreIds(array $storeIds)
    {
        $countryCodes = [];
        foreach ($storeIds as $storeId) {
            $countryCode = $this->scopeConfig
                ->getValue(self::XML_PATH_COUNTRY, ScopeInterface::SCOPE_STORE, $storeId);
            if ($countryCode === CountrySource::DONT_ADD) {
                continue;
            } elseif ($countryCode == CountrySource::DEFAULT_VALUE) {
                $countryCode = $this->scopeConfig->getValue(
                    DirectoryHelper::XML_PATH_DEFAULT_COUNTRY,
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );
            }

            $countryCodes[$storeId] = strtolower($countryCode);
        }

        return $countryCodes;
    }

    /**
     * @param array $storeIds
     * @return array
     */
    private function getLanguagesByStoreIds(array $storeIds)
    {
        $languageCodes = [];
        foreach ($storeIds as $storeId) {
            $languageCode = $this->scopeConfig
                ->getValue(self::XML_PATH_LANGUAGE, ScopeInterface::SCOPE_STORE, $storeId);
            if ($languageCode == LanguageSource::DEFAULT_VALUE) {
                $currentLocale = $this->scopeConfig->getValue(
                    DirectoryHelper::XML_PATH_DEFAULT_LOCALE,
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );

                $currentLocalArray = explode('_', $currentLocale);
                $languageCode = array_shift($currentLocalArray);
            }

            $languageCodes[$storeId] = $languageCode;
        }

        return $languageCodes;
    }

    /**
     * @return bool
     */
    private function isScopeGlobal()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SCOPE) == ScopeSource::SCOPE_GLOBAL;
    }
}
