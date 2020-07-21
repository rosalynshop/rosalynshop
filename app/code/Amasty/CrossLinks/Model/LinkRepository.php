<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model;

use Amasty\CrossLinks\Api\LinkRepositoryInterface;
use Amasty\CrossLinks\Api\LinkInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class LinkRepository
 * @package Amasty\CrossLinks\Model
 */
class LinkRepository implements LinkRepositoryInterface
{
    /**
     * @var \Amasty\CrossLinks\Model\ResourceModel\Link
     */
    protected $_resource;

    /**
     * @var LinkFactory
     */
    protected $_factory;

    /**
     * AbstractGiftCardEntityRepository constructor.
     * @param ResourceModel\Link $resource
     * @param LinkFactory $factory
     */
    public function __construct(
        \Amasty\CrossLinks\Model\ResourceModel\Link $resource,
        \Amasty\CrossLinks\Model\LinkFactory $factory
    ) {
        $this->_resource = $resource;
        $this->_factory = $factory;
    }

    /**
     * @param int $id
     * @return LinkInterface
     * @throws NoSuchEntityException
     */
    public function get($id)
    {
        $entity = $this->_factory->create();
        $this->_resource->load($entity, $id);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('Requested link doesn\'t exist'));
        }
        return $entity;
    }

    /**
     * @param LinkInterface $entity
     * @return $this
     */
    public function save(LinkInterface $entity)
    {
        $this->_resource->save($entity);
        return $this;
    }

    /**
     * @param LinkInterface $entity
     * @return $this
     */
    public function delete(LinkInterface $entity)
    {
        $this->_resource->delete($entity);
        return $this;
    }

    /**
     * @param string $serviceCode
     * @param string $giftCardCode
     * @return LinkInterface
     * @throws NoSuchEntityException
     */
    public function getGiftCardDataByServiceAndCode($serviceCode, $giftCardCode)
    {
        $entity = $this->_factory->create();
        $this->_resource->loadByServiceAndCode($entity, $serviceCode, $giftCardCode);
        if ($entity->getId() === null) {
            throw new NoSuchEntityException(__('Requested link doesn\'t exist'));
        }
        return $entity;
    }
}
