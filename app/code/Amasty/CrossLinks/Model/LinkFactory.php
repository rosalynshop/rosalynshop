<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model;

use Amasty\CrossLinks\Api\LinkInterface;

/**
 * Class LinkFactory
 * @package Amasty\CrossLinks\Model
 */
class LinkFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param array $data
     * @return \Amasty\CrossLinks\Api\LinkInterface
     * @throws \UnexpectedValueException
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create(LinkInterface::class, $data);
    }

    /**
     * @param array $data
     * @return \Amasty\CrossLinks\Api\LinkInterface
     * @throws \UnexpectedValueException
     */
    public function get()
    {
        return $this->_objectManager->get(LinkInterface::class);
    }

}
