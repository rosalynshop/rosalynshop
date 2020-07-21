<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $_storeManager
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig
     */
    private $scopeConfig;

    /**
     * InstallData constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->storeManager->reinitStores();

        $connection = $setup->getConnection();
        foreach ($this->storeManager->getStores() as $store) {
            $data['title'] = 'Imported From Google Sitemap Settings';
            $data['folder_name'] = 'pub/media/google_sitemap_' . $store->getId() . '.xml';
            $data['store_id'] = $store->getId();

            $connection->insert($setup->getTable('amasty_xml_sitemap'), $data);
        }
    }
}
