<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Plugin\View\Page;

class Config
{
    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    public function __construct(
        \Amasty\Meta\Helper\Data $dataHelper
    ) {
        $this->data = $dataHelper;
    }

    public function afterGetKeywords(
        $pageConfig,
        $metaKeywords
    ) {
        $replacedMetaKeywords = $this->data->getReplaceData('meta_keywords');
        if ($replacedMetaKeywords) {
            $metaKeywords = $replacedMetaKeywords;
        }

        return $metaKeywords;
    }

    public function afterGetDescription(
        $pageConfig,
        $metaDescription
    ) {
        $replacedMetaDesc = $this->data->getReplaceData('meta_description');
        if ($replacedMetaDesc) {
            $metaDescription = $replacedMetaDesc;
        }

        return $metaDescription;
    }

    public function afterGetRobots(
        $pageConfig,
        $metaRobots
    ) {
        $replacedMetaRobots = $this->data->getReplaceData('meta_robots');
        if ($replacedMetaRobots) {
            $metaRobots = $replacedMetaRobots;
        }

        return $metaRobots;
    }
}
