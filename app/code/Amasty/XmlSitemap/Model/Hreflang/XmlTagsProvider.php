<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class XmlTagsProvider implements XmlTagsProviderInterface
{
    const ENTITY_PRODUCT = 'product';
    const ENTITY_CATEGORY = 'category';
    const ENTITY_CMS_PAGE = 'cms_page';

    /**
     * @var array[]
     */
    private $hreflangTags = [];

    /**
     * @var DataProviderInterface[]
     */
    private $hreflangProviders;

    /**
     * @var int
     */
    private $currentStoreId;

    /**
     * @var GetCmsPageRelationFieldInterface
     */
    private $getCmsPageRelationField;

    public function __construct(
        GetCmsPageRelationFieldInterface $getCmsPageRelationField,
        array $hreflangProviders,
        $currentStoreId
    ) {
        $this->getCmsPageRelationField = $getCmsPageRelationField;
        $this->hreflangProviders = $hreflangProviders;
        $this->currentStoreId = $currentStoreId;
    }

    /**
     * @inheritdoc
     */
    public function getProductTagAsXml(AbstractModel $product)
    {
        $hreflangTags = $this->getTagsByEntity(self::ENTITY_PRODUCT, $product->getId());
        return $this->getTagsAsXml($hreflangTags);
    }

    /**
     * @inheritdoc
     */
    public function getCategoryTagAsXml(AbstractModel $category)
    {
        $hreflangTags = $this->getTagsByEntity(self::ENTITY_CATEGORY, $category->getId());
        return $this->getTagsAsXml($hreflangTags);
    }

    /**
     * @inheritdoc
     */
    public function getCmsTagAsXml(AbstractModel $page)
    {
        $relField = $this->getCmsPageRelationField->execute();
        $hreflangTags = $this->getTagsByEntity(self::ENTITY_CMS_PAGE, $page->getData($relField));
        return $this->getTagsAsXml($hreflangTags);
    }

    /**
     * @param string[] $tags
     * @return string
     */
    private function getTagsAsXml(array $tags)
    {
        $result = '';
        foreach ($tags as $lang => $url) {
            $result .= PHP_EOL . "<xhtml:link rel=\"alternate\" hreflang=\"$lang\" href=\"$url\"/>";
        }

        return $result;
    }

    /**
     * @param string $entityType
     * @param string|int $id
     * @return array
     */
    private function getTagsByEntity($entityType, $id)
    {
        if (!isset($this->hreflangTags[$entityType])) {
            $this->hreflangTags[$entityType] =
                $this->getHreflangProviderByCode($entityType)->get($this->currentStoreId);
        }

        $tags = isset($this->hreflangTags[$entityType][$id]) ? $this->hreflangTags[$entityType][$id] : [];
        return $tags;
    }

    /**
     * @param string $code
     * @return DataProviderInterface
     * @throws LocalizedException
     */
    private function getHreflangProviderByCode($code)
    {
      if (!isset($this->hreflangProviders[$code])) {
          throw new LocalizedException(__("hreflang prvider for \"$code\" doesn't exist."));
      }

      return $this->hreflangProviders[$code];
    }
}
