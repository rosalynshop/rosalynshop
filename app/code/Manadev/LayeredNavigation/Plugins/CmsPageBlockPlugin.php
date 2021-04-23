<?php

namespace Manadev\LayeredNavigation\Plugins;

use Magento\Cms\Model\Page;
use Magento\Framework\Registry;

class CmsPageBlockPlugin
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    public function __construct(Registry $coreRegistry) {
        $this->coreRegistry = $coreRegistry;
    }

    public function afterGetPage($subject, Page $result) {
        if ($this->coreRegistry->registry('mana_hide_content')) {
            $result->setData('content', '');
        }

        return $result;
    }
}
