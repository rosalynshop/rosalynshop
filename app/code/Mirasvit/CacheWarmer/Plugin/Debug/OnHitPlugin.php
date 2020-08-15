<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Plugin\Debug;

use Magento\Framework\App\PageCache\Identifier as PageCacheIdentifier;
use Magento\Framework\App\PageCache\Kernel;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Registry;
use Magento\PageCache\Model\Cache\Type as CacheType;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\CacheWarmer\Service\BlockGeneratorService;
use Mirasvit\CacheWarmer\Service\Config\HolePunchConfig;
use Mirasvit\Core\Service\CompatibilityService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OnHitPlugin
{
    /**
     * @var PageCacheConfig
     */
    private $config;

    /**
     * @var Version
     */
    private $version;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var BlockGeneratorService
     */
    private $blockGeneratorService;

    /**
     * @var HolePunchConfig
     */
    private $holePunchConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        PageCacheConfig $config,
        Version $version,
        Kernel $kernel,
        BlockGeneratorService $blockGeneratorService,
        HolePunchConfig $holePunchConfig,
        StoreManagerInterface $storeManager,
        Registry $registry,
        CacheType $cacheType,
        PageCacheIdentifier $pageCacheIdentifier
    ) {
        $this->config                = $config;
        $this->version               = $version;
        $this->kernel                = $kernel;
        $this->blockGeneratorService = $blockGeneratorService;
        $this->holePunchConfig       = $holePunchConfig;
        $this->storeManager          = $storeManager;
        $this->registry              = $registry;
        $this->cacheType             = $cacheType;
        $this->pageCacheIdentifier   = $pageCacheIdentifier;
    }

    /**
     * @param Magento\PageCache\Model\App\FrontController\BuiltinPlugin $subject
     * @param \Closure                                                  $pageCache
     * @param Magento\Framework\App\FrontController                     $controller
     * @param \Closure                                                  $proceed
     * @param RequestInterface                                          $request
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\Response\Http
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundAroundDispatch(
        $subject,
        $pageCache,
        $controller,
        \Closure $proceed,
        RequestInterface $request
    ) {
        if (!$this->config->isEnabled() || $this->config->getType() != PageCacheConfig::BUILT_IN) {
            return $proceed($request);
        }

        $this->version->process();
        $result = $this->kernel->load();

        if ($result === false || (is_object($result) && !$result->getBody())) { //forbid return from cache empty page
            $result = $proceed($request);
            if ($result instanceof ResponseHttp) {
                $this->kernel->process($result);
            }

            return $result;
        } else {
            $storeId   = $this->storeManager->getStore()->getId();
            $templates = $this->holePunchConfig->getTemplates($storeId);
            if ($templates) {
                $this->registerData();
                $result = $this->blockGeneratorService->prepareContent($result);
            }
        }

        return $result;
    }

    /**
     * @return void
     */
    private function registerData()
    {
        $this->registry->register(HolePunchConfig::FROM_CACHE, true, true);
        $responseData = $this->cacheType->load($this->pageCacheIdentifier->getValue());

        try {
            $responseData = \Zend_Json::decode($responseData);
        } catch (\Exception $e) {
            $responseData = unserialize($responseData);
        }

        if (!$this->registry->registry(HolePunchConfig::FIND_DATA)
            && ($data = $this->getPatternData($responseData))
            && isset($data[HolePunchConfig::FIND_DATA])) {
            $this->registry->register(
                HolePunchConfig::FIND_DATA,
                unserialize($data[HolePunchConfig::FIND_DATA]),
                true
            );
        }
    }

    /**
     * Need for cms blocks excluding
     * @param stdClass $responseData
     * @return array
     */
    private function getPatternData($responseData)
    {
        if (CompatibilityService::is22()) {
            return (array)$responseData->context->data;
        }
        //Compatibility with M2.1
        $context = $this->getAccessProtected($responseData, 'context');
        $data    = $this->getAccessProtected($context, 'data');

        return (array)$data;
    }

    /**
     * @param stdClass $obj
     * @param string   $prop
     * @return stdClass
     */
    private function getAccessProtected($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property   = $reflection->getProperty($prop);
        $property->setAccessible(true);

        return $property->getValue($obj);
    }
}
