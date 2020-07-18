<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Observer\System;

use Amasty\AdvancedReview\Helper\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Review\Helper\Data;

/**
 * Class ConfigChanged
 * @package Amasty\AdvancedReview\Observer\System
 */
class ConfigChanged implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    private $reinitableConfig;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig,
        \Amasty\AdvancedReview\Helper\Config $config,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {;
        $this->reinitableConfig = $reinitableConfig;
        $this->configWriter = $configWriter;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $name = $observer->getEvent()->getName();
        $catalogValue = $this->getCatalogValue();
        $moduleValue = $this->getAdvancedReviewValue();
        if ($catalogValue != $moduleValue) {
            switch ($name) {
                case 'admin_system_config_changed_section_catalog':
                    $this->saveAdvancedReviewValue($catalogValue);
                    break;
                case 'admin_system_config_changed_section_amasty_advancedreview':
                    $this->saveCatalogValue($moduleValue);
                    break;
            }
        }
    }

    /**
     * @param $value
     */
    private function saveAdvancedReviewValue($value)
    {
        $this->saveConfig(Config::XML_PATH_ALLOW_GUEST, $value);
    }

    /**
     * @param $value
     */
    private function saveCatalogValue($value)
    {
        $this->saveConfig(Data::XML_REVIEW_GUETS_ALLOW, $value);
    }

    /**
     * @param string $path
     * @param string $value
     * @return $this
     */
    private function saveConfig($path, $value)
    {
        $this->configWriter->save($path, $value);
        $this->reinitableConfig->reinit();
        return $this;
    }

    /**
     * @return string
     */
    private function getCatalogValue()
    {
        return $this->config->getGeneralConfig(Data::XML_REVIEW_GUETS_ALLOW);
    }

    /**
     * @return string
     */
    private function getAdvancedReviewValue()
    {
        return $this->config->getGeneralConfig(Config::XML_PATH_ALLOW_GUEST);
    }
}
