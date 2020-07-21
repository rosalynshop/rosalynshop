<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

interface GetLanguageCodesInterface
{
    /**
     * @param int $currentStoreId
     * @return array
     */
    public function execute($currentStoreId);
}
