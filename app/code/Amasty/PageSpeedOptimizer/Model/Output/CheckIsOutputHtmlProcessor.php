<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


namespace Amasty\PageSpeedOptimizer\Model\Output;

class CheckIsOutputHtmlProcessor implements OutputProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function process(&$output)
    {
        if (preg_match('/(<html[^>]*>)(?>.*?<body[^>]*>)/is', $output)) {
            if (preg_match('/(<\/body[^>]*>)(?>.*?<\/html[^>]*>)$/is', $output)) {
                return true;
            }
        }

        return false;
    }
}
