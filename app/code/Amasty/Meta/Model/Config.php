<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Model;

class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_configInheritance = true;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Meta\Model\ResourceModel\Config');
    }

    /**
     * @return object
     */
    public function getCollection()
    {
        $collection = $this->getResourceCollection()->addCategoryFilter();

        return $collection;
    }

    /**
     * @return mixed
     */
    public function getCustomCollection()
    {
        $collection = $this->getResourceCollection()->addCustomFilter();

        return $collection;
    }

    /**
     * @param $url
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigByUrl($url, $storeId = null)
    {
        $collection = $this->getResourceCollection();
        $collection->addUrlFilter($url, $storeId);
        $collection->getSelect()
            ->order("store_id DESC")
            ->order("priority DESC");

        return $collection;
    }

    public function beforeSave()
    {
        if (!$this->getIsCustom()) {
            $this->setIsCustom($this->getCategoryId() === null);
        }

        if ($this->_storeManager->isSingleStoreMode()) {
            $storeId = $this->_storeManager->getStore()->getId();
            $this->setStoreId($storeId);
        }

        if ($this->ifStoreConfigExists($this)) {
            throw new \Exception(__('Template already exists in chosen store'));
        }

        return parent::beforeSave();
    }

    public function ifStoreConfigExists(\Amasty\Meta\Model\Config $item)
    {

        $collection = $this->getResourceCollection()
            ->addFieldToFilter('store_id', $item->getStoreId());

        if ($item->getCategoryId()) {
            $collection
                ->addFieldToFilter('category_id', $item->getCategoryId())
                ->addFieldToFilter('is_custom', 0);
        } else {
            $collection
                ->addFieldToFilter('custom_url', $item->getCustomUrl())
                ->addFieldToFilter('is_custom', 1);
        }

        if ($item->getId()) {
            $collection->addFieldToFilter($this->getIdFieldName(), ['neq' => $item->getId()]);
        }

        return $collection->getSize() > 0;
    }

    public function getRecursionConfigData($paths, $storeId)
    {
        if (empty($paths)) {
            $paths = [[ \Magento\Catalog\Model\Category::TREE_ROOT_ID]];
        }

        $distances = [];

        foreach ($paths as $pathIndex => $path) {
            foreach ($path as $categoryIndex => $category) {
                if (isset($distances[$category])) {
                    $distances[$category]['distance'] = min(
                        $categoryIndex,
                        $distances[$category]['distance']
                    );
                } else {
                    $distances[$category] = [
                        'distance' => $categoryIndex,
                        'path'     => $pathIndex
                    ];
                }
            }
        }

        $queryIds = array_keys($distances);

        $configs = $this->getResourceCollection()
            ->addFieldToFilter('store_id', ['in' => [(int)$storeId, 0]])
            ->addFieldToFilter('category_id', ['in' => $queryIds])
            ->addFieldToFilter('is_custom', 0);

        $foundIds = $configs->getColumnValues('category_id');

        if (empty($foundIds)) {
            return [];
        }

        $bestPaths = [];
        $minDistance = $distances[$foundIds[0]]['distance'];

        foreach ($distances as $id => $category) {
            if (in_array($id, $foundIds)) {
                if ($category['distance'] <= $minDistance) {
                    $minDistance = $category['distance'];
                    $bestPaths[] = $paths[$category['path']];
                }
            }
        }

        $result = [];
        foreach ($bestPaths as $bestPath) {
            $orders = array_flip($bestPath);
            foreach ($configs as $config) {
                if ($config->getCategoryId() == \Magento\Catalog\Model\Category::TREE_ROOT_ID) {
                    // Lowest priority for default category
                    $config->setOrder(sizeof($bestPath));
                    $result [] = $config;
                } elseif (in_array($config->getCategoryId(), $bestPath)) {
                    $config->setOrder($orders[$config->getCategoryId()]);
                    $result [] = $config;
                }
            }
        }

        usort($result, [$this, '_compareConfigs']);

        if (isset($result[0]) && is_object($result[0])) {
            $applied = $this->_registry->registry('ammeta_applied_rule');
            if (!is_array($applied)) {
                $applied = [];
            }
            $applied[] = __('Template (%1) #%2', __('Category'), $result[0]->getId());
            $this->_registry->unregister('ammeta_applied_rule');
            $this->_registry->register('ammeta_applied_rule', $applied);
        }

        if (!$this->_configInheritance) {
            return [$result[0]];
        }

        return $result;
    }

    protected function _compareConfigs($a, $b)
    {
        if ($a->getPriority() != $b->getPriority()) {
            $bOrder = $a->getPriority();
            $aOrder = $b->getPriority();
        } else {
            $aOrder = $a->getOrder();
            $bOrder = $b->getOrder();
        }
        if ($aOrder < $bOrder) {
            return -1;
        } elseif ($aOrder > $bOrder) {
            return 1;
        }

        return ($a->getStoreId() > $b->getStoreId()) ? 1 : -1;
    }
}
