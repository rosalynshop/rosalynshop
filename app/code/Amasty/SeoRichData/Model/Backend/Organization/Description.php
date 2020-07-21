<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Model\Backend\Organization;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Amasty\SeoRichData\Helper\Config as ConfigHelper;

class Description extends ConfigValue
{
    const META_DESCRIPTION_REGEXP = '/<meta name=\"description\" content=\"(.*)\"\s*\/>/i';

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    public function __construct(
        ConfigHelper $configHelper,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
        $this->configHelper = $configHelper;
    }

    /**
     * Retrieve description from meta if url getting
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        if (function_exists('curl_init')
            && $url = filter_var(trim($value), FILTER_VALIDATE_URL)
        ) {
            $value = $this->getDescription($url);
        }
        $this->setData('value', $value);

        return $this;
    }

    /**
     * Send request via curl
     *
     * @param string $url
     * @return bool|string
     */
    private function getDescription($url)
    {
        $result = false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $pageCode = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseCode == 200 && strlen($pageCode) > 2) {
            preg_match_all(self::META_DESCRIPTION_REGEXP, $pageCode, $description);
            if (isset($description[1][0])) {
                $result = $description[1][0];
            }
        }

        return $result;
    }
}
