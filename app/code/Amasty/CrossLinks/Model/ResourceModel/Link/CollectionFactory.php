<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model\ResourceModel\Link;

/**
 * Factory class for @see \Amasty\CrossLinks\Model\ResourceModel\Link\Collection
 */
class CollectionFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * CollectionFactory constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Amasty\CrossLinks\Model\ResourceModel\Link\Collection
     */
    public function create(array $data = array())
    {
        return $this->objectManager->create(Collection::class, $data);
    }

    /**
     * @return \Amasty\CrossLinks\Model\ResourceModel\Link\Collection
     */
    public function get()
    {
        return $this->objectManager->get(Collection::class);
    }
}
