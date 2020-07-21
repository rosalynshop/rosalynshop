<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

use Amasty\XmlSitemap\Model\Source\Hreflang\CmsRelation;
use Magento\Framework\App\Config\ScopeConfigInterface;

class GetCmsPageRelationField implements GetCmsPageRelationFieldInterface
{
    const XML_PATH_RELATION = 'amxmlsitemap/hreflang/cms_relation';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $relationField;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($this->relationField === null) {
            switch ($this->scopeConfig->getValue(self::XML_PATH_RELATION)) {
                case CmsRelation::ID:
                    $this->relationField = 'page_id';
                    break;
                case CmsRelation::IDENTIFIER:
                    $this->relationField = 'identifier';
                    break;
                case CmsRelation::UUID:
                    $this->relationField = GetCmsPageRelationFieldInterface::FIELD_CMS_UUID;
                    break;
            }
        }

        return $this->relationField;
    }
}
