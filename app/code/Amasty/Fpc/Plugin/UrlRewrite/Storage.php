<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Plugin\UrlRewrite;

use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\Refresher;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\UrlRewrite\Model\StorageInterface;

class Storage
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Refresher
     */
    private $refresher;

    public function __construct(
        Config $config,
        Refresher $refresher
    ) {
        $this->config = $config;
        $this->refresher = $refresher;
    }

    public function afterReplace(StorageInterface $object, $result, $urls = null)
    {
        if (!$this->config->isAutoUpdate() || $result === null) {
            return $result;
        }

        $ids = [];

        foreach ($result as $url) {
            if (!in_array($url->getEntityId(), $ids)) {
                $ids[] = $url->getEntityId();
            }
        }

        if (!empty($ids)) {
            $entityType = $url->getEntityType();
            $queueMethod = '';

            switch ($entityType) {
                case Rewrite::ENTITY_TYPE_PRODUCT:
                    $queueMethod = 'queueProductPage';
                    break;

                case Rewrite::ENTITY_TYPE_CATEGORY:
                    $queueMethod = 'queueCategoryPage';
                    break;

                case Rewrite::ENTITY_TYPE_CMS_PAGE:
                    $queueMethod = 'queueCmsPage';
                    break;
            }

            if ($queueMethod) {
                foreach ($ids as $entityId) {
                    $this->refresher->$queueMethod($entityId);
                }
            }
        }

        return $result;
    }
}
