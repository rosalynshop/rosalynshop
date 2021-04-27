<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Plugin\Holepunch;

use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\HolePunch\BlockTagsMarker;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class for marking blocks need to be exluded from cache
 */
class TemplateRenderer
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var BlockTagsMarker
     */
    private $blockTagsMarker;

    public function __construct(
        Config $config,
        BlockTagsMarker $blockTagsMarker
    ) {
        $this->config = $config;
        $this->blockTagsMarker = $blockTagsMarker;
    }

    /**
     * Plugin for php template renderer to mark block with tags
     * @param \Magento\Framework\View\TemplateEngine\Php $subject
     * @param \Closure $proceed
     * @param BlockInterface $block
     * @param string $fileName
     * @param array $dictionary
     *
     * @return string
     */
    public function aroundRender(
        $subject,
        \Closure $proceed,
        BlockInterface $block,
        $fileName,
        array $dictionary = []
    ) {
        $templates = $this->config->getHolePunchBlocks();

        if (!$this->config->isModuleEnabled() || !$templates) {
            return $proceed($block, $fileName, $dictionary);
        }

        foreach ($templates as $template) {
            if ($block instanceof $template['block'] && $fileName === $template['template']) {
                $result = $proceed($block, $fileName, $dictionary);

                if ($template['cms_block_id'] && $template['cms_block_id'] != $block->getBlockId()) {
                    continue;
                }

                return $this->processBlock($block, $result);
            }
        }

        return $proceed($block, $fileName, $dictionary);
    }

    /**
     * @param BlockInterface $block
     * @param string $result
     *
     * @return string
     */
    private function processBlock($block, $result)
    {
        $params = $this->blockTagsMarker->prepareParams($block);
        $result = $this->blockTagsMarker->markBlock($params, $result);

        return $result;
    }
}
