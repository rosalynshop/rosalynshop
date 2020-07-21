<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

interface GetCmsPageRelationFieldInterface
{
    const FIELD_CMS_UUID = 'amasty_hreflang_uuid';

    /**
     * @return string
     */
    public function execute();
}
