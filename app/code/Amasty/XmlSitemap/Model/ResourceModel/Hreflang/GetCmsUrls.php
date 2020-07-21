<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\ResourceModel\Hreflang;

use Amasty\XmlSitemap\Model\Hreflang\GetBaseStoreUrlsInterface;
use Amasty\XmlSitemap\Model\Hreflang\GetCmsPageRelationFieldInterface;
use Amasty\XmlSitemap\Model\Hreflang\GetUrlsInterface;

class GetCmsUrls extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb implements GetUrlsInterface
{
    /**
     * @var GetBaseStoreUrlsInterface
     */
    private $getBaseStoreUrls;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var GetCmsPageRelationFieldInterface
     */
    private $getCmsPageRelationField;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        GetBaseStoreUrlsInterface $getBaseStoreUrls,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        GetCmsPageRelationFieldInterface $getCmsPageRelationField,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->getBaseStoreUrls = $getBaseStoreUrls;
        $this->metadataPool = $metadataPool;
        $this->getCmsPageRelationField = $getCmsPageRelationField;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cms_page', 'page_id');
    }

    /**
     * @inheritdoc
     */
    public function execute($storeIds, array $ids = null)
    {
        $linkField = $this->getLinkField();
        $relationField = $this->getCmsPageRelationField->execute();
        $select = $this->getConnection()->select()
            ->from(
                ['main_table' => $this->getMainTable()],
                ['id' => $relationField, 'url' => 'identifier']
            )->join(
                ['page_store' => $this->getTable('cms_page_store')],
                "main_table.$linkField = page_store.$linkField",
                ['store_id']
            )->where('store_id IN(?)', array_merge($storeIds, [0]))
            ->where('is_active = 1');
        if ($relationField === GetCmsPageRelationFieldInterface::FIELD_CMS_UUID) {
            $select->where("$relationField != ''");
        }

        if($linkField != $this->getIdFieldName()) { //get only latest entries for non-CE Magento versions with staging.
            $select->order("main_table.$linkField DESC");
        }

        if (!empty($ids)) {
            $select->where("$relationField IN (?)", $ids);
        }

        $urls = [];
        $storesBaseUrl = $this->getBaseStoreUrls->execute();
        $pages = $this->getConnection()->fetchAll($select);
        foreach ($pages as $page) {
            $key = $page['id'] . '_' . $page['store_id'];
            if (!key_exists($key, $urls)) { //get only latest entries for non-CE Magento versions with staging.
                $urls[$key] = null;
                if ($page['store_id'] === '0') {
                    foreach ($storeIds as $storeId) {
                        $item = $page;
                        $item['url'] = $storesBaseUrl[$storeId] . $page['url'];
                        $urls[] = $item;
                    }
                } else {
                    $page['url'] = $storesBaseUrl[$page['store_id']] . $page['url'];
                    $urls[] = $page;
                }
            }
        }

        $urls = array_filter($urls);
        return $urls;
    }

    /**
     * @return string
     */
    private function getLinkField()
    {
        return $this->metadataPool
            ->getMetadata(\Magento\Cms\Api\Data\PageInterface::class)->getLinkField();
    }
}
