<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\HolePunch;

/**
 * Registry for storing block arguments. Will be used in tags generation
 */
class BlockArgumentsRegistry
{
    /**
     * @var array
     */
    private $storage = [];

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @param array $args
     */
    public function setBlockArgs($block, $args)
    {
        $blockType = $block->getType();
        $layoutName = $block->getNameInLayout();

        if (!isset($this->storage[$blockType][$layoutName])) {
            $this->checkForObjects($args);
            $this->storage[$blockType][$layoutName] = $args;
        }
    }

    /**
     * @param string $blockType
     * @param string $layoutName
     *
     * @return array
     */
    public function getBlockArgs($blockType, $layoutName)
    {
        $args = [];

        if (isset($this->storage[$blockType][$layoutName])) {
            $args = $this->storage[$blockType][$layoutName];
        }

        return $args;
    }

    /**
     * @param array|string|object $args
     */
    private function checkForObjects(&$args)
    {
        if (is_array($args)) {
            foreach ($args as $key => &$value) {
                if (is_array($value)) {
                    $this->checkForObjects($value);
                }
                if (is_object($value)) {
                    $args[$key] = [
                        'is_object' => true,
                        'class' => get_class($value)
                    ];
                }
            }
        }

        if (is_object($args)) {
            $args = [
                'is_object' => true,
                'class' => get_class($args)
            ];
        }
    }
}
