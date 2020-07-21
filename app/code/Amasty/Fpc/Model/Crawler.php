<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Amasty\Fpc\Model\Queue\Page;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Group;
use Magento\Framework\App\Response\Http;
use Magento\Store\Model\StoreCookieManager;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use \Magento\Framework\App\Http\Context as HttpContext;
use Amasty\Fpc\Helper\Http as HttpHelper;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * phpcs:ignoreFile
 */
class Crawler
{
    const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36';

    const USER_AGENT_EXTENSION = 'Amasty_Fpc';

    const SESSION_COOKIE = 'PHPSESSID';
    const SESSION_NAME = 'amasty-fpc-crawler';

    private $multipleCurl;

    private $curlList;

    protected $curl;
    protected $headers = [];
    protected $cookies = [];

    /**
     * @var
     */
    protected $defaultCurrency;

    /**
     * @var Log
     */
    private $crawlerLog;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var \Magento\PageCache\Model\Cache\Type
     */
    private $fpc;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var QueuePageRepository
     */
    private $queuePageRepository;

    public function __construct(
        Log $crawlerLog,
        StoreManagerInterface $storeManager,
        Config $config,
        LoggerInterface $logger,
        \Magento\Framework\App\Http\ContextFactory $httpContextFactory,
        \Magento\PageCache\Model\Cache\Type $fpc,
        ProductMetadataInterface $productMetadata,
        QueuePageRepository $queuePageRepository
    ) {
        $this->crawlerLog = $crawlerLog;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->logger = $logger;
        $this->httpContext = $httpContextFactory->create();
        $this->defaultCurrency = $this->storeManager->getWebsite()->getDefaultStore()->getDefaultCurrency()->getCode();
        $this->fpc = $fpc;
        $this->productMetadata = $productMetadata;
        $this->queuePageRepository = $queuePageRepository;
    }

    public function processPage(Page $page, $customerGroup, $storeId, $currency, $mobile = false)
    {
        $this->setParams($customerGroup, $storeId, $currency);

        $loadStart = microtime(true);
        $status = $this->request($page->getUrl(), $mobile);
        $loadTime = microtime(true) - $loadStart;

        $this->crawlerLog->add([
            'url' => $page->getUrl(),
            'customer_group' => $customerGroup,
            'store' => $storeId,
            'currency' => $currency,
            'rate' => $page->getRate(),
            'status' => $status,
            'load_time' => round($loadTime),
            'mobile' => $mobile
        ]);

        $this->queuePageRepository->delete($page);

        curl_close($this->curl);

        return $status;
    }

    public function runMultipleCurl($customerGroup, $storeId, $currency, $mobile = false)
    {
        $pagesCrawled = 0;
        $this->setParams($customerGroup, $storeId, $currency);
        do {
            $status = curl_multi_exec($this->multipleCurl, $active);
            if ($active) {
                // Wait a short time for more activity
                curl_multi_select($this->multipleCurl);
            }
        } while ($active && $status == CURLM_OK);

        foreach ($this->curlList as $key => $curl) {
            $url = $key;
            $result = curl_getinfo($curl);

            $this->crawlerLog->add([
                'url' => $url,
                'customer_group' => $customerGroup,
                'store' => $storeId,
                'currency' => $currency,
                'status' => $result['http_code'] === CURLM_OK ? 200 : $result['http_code'],
                'load_time' => isset($result['total_time']) ? round($result['total_time']) : 0,
                'mobile' => $mobile
            ]);
            $pagesCrawled++;

            curl_multi_remove_handle($this->multipleCurl, $curl);
        }
        curl_multi_close($this->multipleCurl);
        $this->curlList = [];

        return $pagesCrawled;
    }

    public function initMultipleCurl(&$pages, $mobile)
    {
        $processesNumber = $this->config->getProcessesNumber();
        $curlsInitialized = 0;
        $this->multipleCurl = curl_multi_init();
        foreach ($pages as $key => $page) {
            if ($curlsInitialized >= $processesNumber) {
                break;
            }
            $url = $page->getUrl();
            $this->curlList[$url] = $this->request($url, $mobile, true);
            curl_setopt($this->curlList[$url], CURLOPT_TIMEOUT, 1);
            curl_multi_add_handle($this->multipleCurl, $this->curlList[$url]);

            $curlsInitialized++;
            unset($pages[$key]);
            $this->queuePageRepository->delete($page);
        }
    }

    protected function setParams($customerGroup, $storeId, $currency)
    {
        $this->headers = [];
        $this->cookies = [];

        if ($customerGroup) {
            $this->headers[HttpHelper::CUSTOMER_GROUP_HEADER] = $customerGroup;
        }

        if ($currency) {
            $this->headers[HttpHelper::CURRENCY_HEADER] = $currency;
        }

        if ($storeId) {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();
            $this->cookies[StoreCookieManager::COOKIE_NAME] = $storeCode;
        }

        $this->initVaryCookie($customerGroup, $storeId, $currency);
    }

    protected function initVaryCookie($customerGroup, $storeId, $currency)
    {
        if (!$customerGroup && !$storeId && !$currency) {
            return;
        }

        $this->httpContext->setValue(
            CustomerContext::CONTEXT_GROUP,
            $customerGroup ?: Group::NOT_LOGGED_IN_ID,
            Group::NOT_LOGGED_IN_ID
        );

        $this->httpContext->setValue(
            CustomerContext::CONTEXT_AUTH,
            (bool)$customerGroup,
            false
        );

        $this->httpContext->setValue(
            HttpContext::CONTEXT_CURRENCY,
            $currency ?: $this->defaultCurrency,
            $this->defaultCurrency
        );

        if ($storeId) {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();

            $this->httpContext->setValue(
                StoreManagerInterface::CONTEXT_STORE,
                $storeCode,
                $this->storeManager->getDefaultStoreView()->getCode()
            );
        } else {
            $this->httpContext->unsValue(StoreManagerInterface::CONTEXT_STORE);
        }

        $this->cookies[Http::COOKIE_VARY_STRING] = $this->httpContext->getVaryString();
    }

    protected function setCookies()
    {
        $this->cookies[self::SESSION_COOKIE] = self::SESSION_NAME;

        $cookies = [];

        foreach ($this->cookies as $name => $value) {
            $cookies [] = "$name=$value;";
        }

        curl_setopt($this->curl, CURLOPT_COOKIE, implode(' ', $cookies));
    }

    protected function setHeaders()
    {
        $this->headers[HttpHelper::STATUS_HEADER] = 'crawl';

        $headers = [];
        foreach ($this->headers as $name => $value) {
            $headers [] = "$name: $value";
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    }

    protected function request($url, $mobile, $multiple = false)
    {
        $this->curl = curl_init($url);

        $this->setCookies();
        $this->setHeaders();

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);

        if ($this->config->isHttpAuth()) {
            $login = trim($this->config->getLogin());
            $password = trim($this->config->getPassword());

            if ($login && $password) {
                curl_setopt($this->curl, CURLOPT_USERPWD, $login . ":" . $password);
            }
        }

        if ($this->config->isSkipVerification()) {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        if ($mobile) {
            // retrieve MOBILE version
            $userAgent = $this->config->getMobileAgent();
        } else {
            $userAgent = self::USER_AGENT;
        }

        $userAgent .= ' ' . self::USER_AGENT_EXTENSION;

        curl_setopt($this->curl, CURLOPT_USERAGENT, $userAgent);

        if ($multiple) {
            return $this->curl;
        }

        curl_exec($this->curl);

        if ($error = curl_error($this->curl)) {
            $this->logger->error($error);
        }

        $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        return $status;
    }

    public function isAlreadyCached($url)
    {
        if ($this->config->isVarnishEnabled()) {
            return false;
        }

        /**
         * FPC key generation
         * For explanation @see \Magento\Framework\App\PageCache\Identifier::getValue
         */
        $varyString = isset($this->cookies[Http::COOKIE_VARY_STRING])
            ? $this->cookies[Http::COOKIE_VARY_STRING]
            : null;

        $hashData = [
            strpos($url, 'https://') === 0,
            $url,
            $varyString
        ];

        $identifier = sha1(json_encode($hashData));

        if ($this->fpc->test($identifier)) {
            return HttpHelper::STATUS_ALREADY_CACHED;
        }
    }
}
