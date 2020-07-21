<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

use Magento\Framework\ObjectManagerInterface;

class XmlTagsProviderFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param $storeId
     * @return XmlTagsProviderInterface
     */
    public function get($storeId)
    {
        return $this->objectManager->create(XmlTagsProvider::class, ['currentStoreId' => (int)$storeId]);
    }
}
