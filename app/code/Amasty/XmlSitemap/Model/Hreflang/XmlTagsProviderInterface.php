<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Hreflang;

use Magento\Framework\Model\AbstractModel;

interface XmlTagsProviderInterface
{
    /**
     * @param AbstractModel $product
     * @return string
     */
    public function getProductTagAsXml(AbstractModel $product);

    /**
     * @param AbstractModel $category
     * @return string
     */
    public function getCategoryTagAsXml(AbstractModel $category);

    /**
     * @param AbstractModel $page
     * @return string
     */
    public function getCmsTagAsXml(AbstractModel $page);
}
