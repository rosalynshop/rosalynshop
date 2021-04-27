<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite as UrlRewrite;

class Category extends Rewrite
{
    /**
     * @var string
     */
    protected $rewriteType = UrlRewrite::ENTITY_TYPE_CATEGORY;
}
