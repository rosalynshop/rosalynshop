<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\HolePunch;

/**
 * Class for marking blocks that need to be excluded from cache with special tags storing block data
 */
class BlockTagsMarker
{
    const START_TAG_BEGIN = '<!--[amasty_holepunch_tag_start_begin';
    const START_TAG_END = 'amasty_holepunch_tag_start_end]-->';
    const END_TAG_BEGIN = '<!--[amasty_holepunch_tag_end_begin';
    const END_TAG_END = 'amasty_holepunch_tag_end_end]-->';
    const SEPARATOR = '___';
    const SEARCH_PATTERN = '/<!--\[amasty_holepunch_tag_start_begin(.*?)amasty_holepunch_tag_start_end]-->/i';
    const WIDGET_BLOCK_REPLACER_PATTERN = '/<\/div>\n?<!--\[amasty_holepunch_tag_end_begin/im';

    const BLOCK_CLASS = 'block_class';
    const MODULE_NAME = 'module_name';
    const LAYOUT_NAME = 'layout_name';
    const TEMPLATE = 'template';
    const TEMPLATE_FILE = 'template_file';
    const CMS_WIDGET_ID = 'cms_widget_id';
    const ARGUMENTS = 'arguments';

    /**
     * @var \Amasty\Fpc\Model\HolePunch\BlockArgumentsRegistry
     */
    private $argumentsRegistry;

    public function __construct(
        BlockArgumentsRegistry $argumentsRegistry
    ) {
        $this->argumentsRegistry = $argumentsRegistry;
    }

    /**
     * Mark block with tags to exclude it from cache
     * @param array $params
     * @param string $result
     */
    public function markBlock($params, $result)
    {
        return $this->getStartTag($params) . $result . $this->getEndTag($params);
    }

    /**
     * Prepare params to be stored in block marker tag
     * @param \Magento\Framework\View\Element\BlockInterface $block
     *
     * @return array
     */
    public function prepareParams($block)
    {
        $params = [];
        $params[self::BLOCK_CLASS] = strpos($block->getType(), '_') ? $block->getType() : get_class($block);
        $params[self::MODULE_NAME] = $block->getModuleName();
        $params[self::LAYOUT_NAME] = $block->getNameInLayout();
        $params[self::TEMPLATE] = $block->getTemplate();
        $params[self::TEMPLATE_FILE] = $block->getTemplateFile();
        $params[self::CMS_WIDGET_ID] = $this->getWidgetId($block);
        $params[self::ARGUMENTS] = json_encode(
            $this->argumentsRegistry
            ->getBlockArgs($block->getType(), $block->getNameInLayout())
        );

        return $params;
    }

    /**
     * @param \Magento\Framework\View\Element\BlockInterface $block
     *
     * @return bool|string
     */
    private function getWidgetId($block)
    {
        $widgetId = false;

        if ($block->getModuleName() == 'Magento_Cms') {
            $widgetId = $block->getBlockId();
        }

        return $widgetId;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getStartTag($params)
    {
        return self::START_TAG_BEGIN . $this->getHash($params) . self::START_TAG_END;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getEndTag($params)
    {
        return self::END_TAG_BEGIN . $this->getHash($params) . self::END_TAG_END;
    }

    /**
     * Get hash for params used in tags
     * @param array $params
     *
     * @return string
     */
    private function getHash($params)
    {
        $hash = $params[self::BLOCK_CLASS] . self::SEPARATOR
            . $params[self::MODULE_NAME] . self::SEPARATOR
            . $params[self::LAYOUT_NAME] . self::SEPARATOR
            . $params[self::TEMPLATE] . self::SEPARATOR
            . $params[self::TEMPLATE_FILE] . self::SEPARATOR
            . $params[self::CMS_WIDGET_ID] . self::SEPARATOR
            . $params[self::ARGUMENTS];

        return $this->processHash($hash, true);
    }

    /**
     * Encode or decode hasch from tags
     * @param bool $encode
     * @param string $value
     *
     * @return bool|string
     */
    public function processHash($value, $encode = false)
    {
        if ($encode) {
            $value = bin2hex($value);
        } else {
            $value = hex2bin($value);
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getKeysArray()
    {
        return [
            self::BLOCK_CLASS,
            self::MODULE_NAME,
            self::LAYOUT_NAME,
            self::TEMPLATE,
            self::TEMPLATE_FILE,
            self::CMS_WIDGET_ID,
            self::ARGUMENTS
        ];
    }
}
