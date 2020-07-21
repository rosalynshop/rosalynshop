<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

use Magento\Framework\Exception\LocalizedException;

class DataProvider implements DataProviderInterface
{
    /**
     * @var GetLanguageCodesInterface
     */
    private $getHreflangLanguageCodes;

    /**
     * @var GetUrlsInterface
     */
    private $getHreflangUrls;

    /**
     * @var string[]
     */
    private $languageCodes;

    public function __construct(
        GetLanguageCodesInterface $getHreflangLanguageCodes,
        GetUrlsInterface $getHreflangUrls
    ) {
        $this->getHreflangLanguageCodes = $getHreflangLanguageCodes;
        $this->getHreflangUrls = $getHreflangUrls;
    }

    /**
     * @inheritdoc
     */
    public function get($currentStoreId, array $entityIds = null)
    {
        $this->languageCodes = $this->getHreflangLanguageCodes->execute($currentStoreId);
        $storeIds = array_keys($this->languageCodes);
        $result = [];
        if (empty($storeIds)) {
            return $result;
        }

        $urls = $this->getHreflangUrls->execute($storeIds, $entityIds);
        foreach ($urls as $urlData) {
            if (!isset($urlData['store_id']) && !isset($urlData['url'])) {
                throw new LocalizedException(
                    __("urlData array doesn't contain required fields. Check GetUrlsInterface class.")
                );
            }

            if (!isset($result[$urlData['id']])) {
                $result[$urlData['id']] = [];
            }

            if ($urlData['store_id'] == 0) {
                foreach ($storeIds as $storeId) {
                    $language = $this->getLanguageByStoreId($storeId);
                    $result[$urlData['id']][$language] = $urlData['url'];
                }
            } else {
                $language = $this->getLanguageByStoreId($urlData['store_id']);
                $result[$urlData['id']][$language] = $urlData['url'];
            }
        }

        return $result;
    }

    /**
     * @param int|string $storeId
     * @return string
     * @throws LocalizedException
     */
    private function getLanguageByStoreId($storeId)
    {
        if (!isset($this->languageCodes[$storeId])) {
            throw new LocalizedException(
                __("languageCodes array doesn't storeId #%1. Check GetLanguageCodesInterface class.", $storeId)
            );
        }

        $language = $this->languageCodes[$storeId];
        return $language;
    }
}
