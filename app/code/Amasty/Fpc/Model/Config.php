<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigDataCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\DataObject;
use Magento\PageCache\Model\Config as VarnishConfig;

class Config extends DataObject
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const PATH_PREFIX = 'amasty_fpc/';

    const IS_ENABLED = 'general/enabled';

    const AUTO_UPDATE = 'general/auto_update';

    const QUEUE_REGENERATE = 'general/queue_regenerate';

    const FLUSHES_LOG = 'general/enable_flushes_log';

    const IGNORE_CLASSES = 'general/ignore_classes';

    const CUSTOMER_ACTIVITY = 'general/customer_activity';

    const GENERATION_SOURCE = 'source_and_priority/source';

    const PAGE_TYPES = 'source_and_priority/page_types';

    const FILE_PATH = 'source_and_priority/file_path';

    const SITEMAP_PATH = 'source_and_priority/sitemap_path';

    const MULTIPLE_CURL = 'performance_settings/multiple_curl';

    const PROCESSES_NUMBER = 'performance_settings/processes_number';

    const MAX_QUEUE_SIZE = 'performance_settings/max_queue_size';

    const BATCH_SIZE = 'performance_settings/batch_size';

    const DELAY = 'performance_settings/delay';

    const LOG_SIZE = 'performance_settings/log_size';

    const EXCLUDE_PAGES = 'combinations/ignore_list';

    const PROCESS_MOBILE = 'combinations/process_mobile';

    const MOBILE_AGENT = 'combinations/mobile_agent';

    const USER_AGENTS = 'combinations/user_agents';

    const HOLE_PUNCH  = 'hole_punch/hole_punch';

    const HTTP_AUTHENTICATION = 'connection/http_auth';

    const LOGIN = 'connection/login';

    const PASSWORD = 'connection/password';

    const SKIP_VERIFICATION = 'connection/skip_verification';

    const PROCESS_CRON = 'performance_settings/process_cron';

    const GENERATE_CRON = 'performance_settings/generate_cron';

    const SHOW_STATUS = 'debug/show_status';

    const IPS = 'debug/ips';

    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ConfigDataCollectionFactory
     */
    private $configCollection;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * Config constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ConfigDataCollectionFactory $configCollection
     * @param HttpRequest $request
     * @param \Amasty\Base\Model\Serializer $serializer
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigDataCollectionFactory $configCollection,
        HttpRequest $request,
        \Amasty\Base\Model\Serializer $serializer,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($data);
        $this->request = $request;
        $this->serializer = $serializer;
        $this->configCollection = $configCollection;
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function getValue($path)
    {
        return $this->scopeConfig->getValue(self::PATH_PREFIX . $path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isSetFlag($path)
    {
        return $this->scopeConfig->isSetFlag(self::PATH_PREFIX . $path);
    }

    /**
     * @param string $enabledSetting
     * @param string $combinationsSetting
     *
     * @return array
     */
    protected function getCombinations($enabledSetting, $combinationsSetting)
    {
        if (!$this->isSetFlag('combinations/' . $enabledSetting)) {
            return [];
        }

        $values = $this->getValue('combinations/' . $combinationsSetting);

        return $this->split($values);
    }

    /**
     * Convert comma separated string to array
     *
     * @param $string
     *
     * @return array
     */
    protected function split($string)
    {
        $string = trim($string);

        if ($string == "") {
            return [];
        } else {
            return explode(',', $string);
        }
    }

    /**
     * Return all config items by path
     *
     * @param string $path
     *
     * @return array
     */
    public function getAllValuesByPath($path)
    {
        $configCollection = $this->configCollection->create();
        $configCollection->addFieldToFilter('path', ['eq' => $path]);

        return $configCollection->getData();
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->isSetFlag(self::IS_ENABLED);
    }

    /**
     * @return bool
     */
    public function isAutoUpdate()
    {
        return $this->isSetFlag(self::AUTO_UPDATE);
    }

    /**
     * @return bool
     */
    public function isMultipleCurl()
    {
        return $this->isSetFlag(self::MULTIPLE_CURL);
    }

    /**
     * @return int
     */
    public function getProcessesNumber()
    {
        return (int)$this->getValue(self::PROCESSES_NUMBER);
    }

    /**
     * @return mixed
     */
    public function getQueueAfterGenerate()
    {
        return $this->getValue(self::QUEUE_REGENERATE);
    }

    /**
     * @return bool
     */
    public function isEnableFlushesLog()
    {
        return $this->isSetFlag(self::FLUSHES_LOG);
    }

    /**
     * @return bool
     */
    public function isLogCustomerActivity()
    {
        return $this->isSetFlag(self::CUSTOMER_ACTIVITY);
    }

    /**
     * @return string
     */
    public function getSourceType()
    {
        return $this->getValue(self::GENERATION_SOURCE);
    }

    /**
     * @return string
     */
    public function getQueueLimit()
    {
        return $this->getValue(self::MAX_QUEUE_SIZE);
    }

    /**
     * @return int
     */
    public function getBatchSize()
    {
        return (int)$this->getValue(self::BATCH_SIZE);
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return (int)$this->getValue(self::DELAY);
    }

    /**
     * @return int
     */
    public function getLogSize()
    {
        return (int)$this->getValue(self::LOG_SIZE);
    }

    /**
     * @return array
     */
    public function getExcludePages()
    {
        return $this->serializer->unserialize($this->getValue(self::EXCLUDE_PAGES));
    }

    /**
     * @return array
     */
    public function getIgnoreClasses()
    {
        return $this->serializer->unserialize($this->getValue(self::IGNORE_CLASSES));
    }

    /**
     * @return array
     */
    public function getHolePunchBlocks()
    {
        $holePunchBlocks = $this->getValue(self::HOLE_PUNCH);

        if ($holePunchBlocks) {
            return (array)$this->serializer->unserialize($holePunchBlocks);
        }

        return [];
    }

    /**
     * @return bool
     */
    public function isProcessMobile()
    {
        return (bool)$this->getValue(self::PROCESS_MOBILE);
    }

    /**
     * @return string
     */
    public function getMobileAgent()
    {
        return $this->getValue(self::MOBILE_AGENT);
    }

    /**
     * @return string
     */
    public function getUserAgents()
    {
        return $this->getValue(self::USER_AGENTS);
    }

    /**
     * @return bool
     */
    public function isHttpAuth()
    {
        return $this->isSetFlag(self::HTTP_AUTHENTICATION);
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->getValue(self::LOGIN);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getValue(self::PASSWORD);
    }

    /**
     * @return bool
     */
    public function isSkipVerification()
    {
        return $this->isSetFlag(self::SKIP_VERIFICATION);
    }

    /**
     * @return array
     */
    public function getStores()
    {
        return $this->getCombinations('switch_stores', 'stores');
    }

    /**
     * @return array
     */
    public function getCurrencies()
    {
        return $this->getCombinations('switch_currencies', 'currencies');
    }

    /**
     * @return array
     */
    public function getCustomerGroups()
    {
        return $this->getCombinations('switch_customer_groups', 'customer_groups');
    }

    /**
     * @return bool
     */
    public function isVarnishEnabled()
    {
        return $this->scopeConfig->getValue(VarnishConfig::XML_PAGECACHE_TYPE) == VarnishConfig::VARNISH;
    }

    /**
     * @return array
     */
    public function getDebugIps()
    {
        $ips = $this->getValue(self::IPS);
        $ips = preg_split('/\s*,\s*/', trim($ips), -1, PREG_SPLIT_NO_EMPTY);

        return $ips;
    }

    /**
     * @return bool
     */
    public function canDisplayStatus()
    {
        if (!$this->isSetFlag(self::SHOW_STATUS)) {
            return false;
        }

        if ($allowedIps = $this->getDebugIps()) {
            $clientIp = $this->request->getClientIp(true);
            if (!in_array($clientIp, $allowedIps)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    public function getPagesConfig()
    {
        $config = $this->getValue(self::PAGE_TYPES);

        return $this->serializer->unserialize($config);
    }
}
