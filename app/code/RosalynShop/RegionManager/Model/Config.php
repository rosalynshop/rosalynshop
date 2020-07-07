<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 * @package RosalynShop\RegionManager\Model
 */
class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var null
     */
    protected $_storeId = null;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->_storeId = (int)$this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    public function getEnableExtensionYesNo()
    {
        return $this->_scopeConfig->getValue("regionmanager_config/general_config_group/enabled_extension_yes_no", ScopeInterface::SCOPE_STORE, (int)$this->getStoreId());
    }
}