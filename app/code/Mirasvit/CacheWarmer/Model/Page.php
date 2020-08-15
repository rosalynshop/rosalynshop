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



namespace Mirasvit\CacheWarmer\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\Core\Service\CompatibilityService;

class Page extends AbstractModel implements PageInterface
{
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Page::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(PageInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->getData(PageInterface::URI);
    }

    /**
     * {@inheritdoc}
     */
    public function setUri($value)
    {
        return $this->setData(PageInterface::URI, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheId()
    {
        return $this->getData(PageInterface::CACHE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheId($value)
    {
        return $this->setData(PageInterface::CACHE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageType()
    {
        return $this->getData(PageInterface::PAGE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageType($value)
    {
        return $this->setData(PageInterface::PAGE_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(PageInterface::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($value)
    {
        return $this->setData(PageInterface::PRODUCT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId()
    {
        return $this->getData(PageInterface::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryId($value)
    {
        return $this->setData(PageInterface::CATEGORY_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setVaryData($value)
    {
        if (is_array($value)) {
            ksort($value);
            $value = \Zend_Json::encode($value);
        }

        return $this->setData(PageInterface::VARY_DATA, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getVaryString()
    {
        $data = $this->getVaryData();

        if (!empty($data)) {
            ksort($data);

            return sha1(CompatibilityService::is21() ? serialize($data) : \Zend_Json::encode($data));
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getVaryData()
    {
        try {
            $value = \Zend_Json::decode($this->getData(PageInterface::VARY_DATA));
        } catch (\Exception $e) {
            $value = [];
        }

        if (is_array($value)) {
            ksort($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttempts()
    {
        return $this->getData(PageInterface::ATTEMPTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttempts($value)
    {
        return $this->setData(PageInterface::ATTEMPTS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPopularity()
    {
        return $this->getData(PageInterface::POPULARITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setPopularity($value)
    {
        return $this->setData(PageInterface::POPULARITY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWarmRuleVersion()
    {
        return $this->getData(PageInterface::WARM_RULE_VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setWarmRuleVersion($value)
    {
        return $this->setData(PageInterface::WARM_RULE_VERSION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWarmRuleIds()
    {
        return array_filter(explode(',', $this->getData(PageInterface::WARM_RULE_IDS)));
    }

    /**
     * {@inheritdoc}
     */
    public function setWarmRuleIds(array $value)
    {
        return $this->setData(PageInterface::WARM_RULE_IDS, implode(',', $value));
    }


    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        try {
            $value = \Zend_Json::decode($this->getData(PageInterface::HEADERS));
        } catch (\Exception $e) {
            $value = [];
        }

        if (is_array($value)) {
            ksort($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders(array $value)
    {
        if (is_array($value)) {
            ksort($value);
            $value = \Zend_Json::encode($value);
        }

        return $this->setData(PageInterface::HEADERS, $value);
    }
}
