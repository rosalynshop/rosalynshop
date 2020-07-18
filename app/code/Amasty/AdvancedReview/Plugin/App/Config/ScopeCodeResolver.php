<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\App\Config;

use Magento\Framework\App\ScopeResolverPool;

/**
 * Class ScopeCodeResolver
 * @package Amasty\AdvancedReview\Plugin\App\Config
 */
class ScopeCodeResolver
{
    /**
     * @var bool
     */
    private $needClean = false;

    /**
     * @var ScopeResolverPool
     */
    private $scopeResolverPool;

    /**
     * @var null|string
     */
    private $scopeType = null;

    /**
     * @var null|string
     */
    private $scopeCode = null;

    public function __construct(
        ScopeResolverPool $scopeResolverPool
    ) {
        $this->scopeResolverPool = $scopeResolverPool;
    }

    /**
     * @param \Magento\Framework\App\Config\ScopeCodeResolver $scopeCodeResolver
     * @param string $scopeType
     * @param string $scopeCode
     *
     * @return array
     */
    public function beforeResolve(
        $scopeCodeResolver,
        $scopeType,
        $scopeCode
    ) {
        if ($this->isNeedClean() && method_exists($scopeCodeResolver, 'clean')) {
            $scopeCodeResolver->clean();
        } else {
            $this->scopeType = $scopeType;
            $this->scopeCode = $scopeCode;
        }

        return [$scopeType, $scopeCode];
    }

    /**
     * @param \Magento\Framework\App\Config\ScopeCodeResolver $scopeCodeResolver
     * @param string $resolverScopeCode
     *
     * @return string
     */
    public function afterResolve(
        $scopeCodeResolver,
        $resolverScopeCode
    ) {
        //support old version when clean method not exist
        if ($this->isNeedClean() && $this->scopeType) {
            $scopeResolver = $this->scopeResolverPool->get($this->scopeType);
            $resolverScopeCode = $scopeResolver->getScope($this->scopeCode);
            if ($resolverScopeCode instanceof \Magento\Framework\App\ScopeInterface) {
                $resolverScopeCode = $resolverScopeCode->getCode();
            }
        }

        return $resolverScopeCode;
    }

    /**
     * @param bool $needClean
     */
    public function setNeedClean($needClean)
    {
        $this->needClean = $needClean;
    }

    /**
     * @return bool
     */
    public function isNeedClean()
    {
        return $this->needClean;
    }
}
