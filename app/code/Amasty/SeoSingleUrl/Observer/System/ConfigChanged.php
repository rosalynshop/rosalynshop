<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Observer\System;

use Amasty\SeoSingleUrl\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ConfigChanged implements ObserverInterface
{
    const CATALOG_PATH       = 'catalog/seo/product_use_categories';
    const AMASTY_SEOURL_PATH = 'amasty_seourl/general/product_use_categories';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    private $reinitableConfig;

    public function __construct(
        Data $helper,
        \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        $this->helper = $helper;
        $this->reinitableConfig = $reinitableConfig;
        $this->configWriter = $configWriter;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $name = $observer->getEvent()->getName();
        $catalogValue = $this->getCatalogValue();
        $moduleValue = $this->getSeoUrlValue();
        if ($catalogValue != $moduleValue) {
            switch ($name) {
                case 'admin_system_config_changed_section_catalog':
                    $this->saveSeoUrlValue($catalogValue);
                    break;
                case 'admin_system_config_changed_section_amasty_seourl':
                    $this->saveCatalogValue($moduleValue);
                    break;
            }
        }
    }

    /**
     * @param $value
     */
    private function saveSeoUrlValue($value)
    {
        $this->saveConfig($this::AMASTY_SEOURL_PATH, $value);
    }

    /**
     * @param $value
     */
    private function saveCatalogValue($value)
    {
        $this->saveConfig($this::CATALOG_PATH, $value);
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
        return $this->helper->getConfig($this::CATALOG_PATH);
    }

    /**
     * @return string
     */
    private function getSeoUrlValue()
    {
        return $this->helper->getConfig($this::AMASTY_SEOURL_PATH);
    }
}
