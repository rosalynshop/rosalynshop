<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Plugin\Holepunch;

use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\HolePunch\BlockArgumentsRegistry;

/**
 * Class for saving block arguments for tags generation
 */
class BlockArguments
{
    /**
     * @var BlockArgumentsRegistry
     */
    private $blockArgumentsRegistry;

    /**
     * @var Config
     */
    private $config;

    /**
     * BlockArguments constructor.
     *
     * @param BlockArgumentsRegistry $blockArgumentsRegistry
     * @param Config $config
     */
    public function __construct(
        BlockArgumentsRegistry $blockArgumentsRegistry,
        Config $config
    ) {
        $this->blockArgumentsRegistry = $blockArgumentsRegistry;
        $this->config = $config;
    }

    /**
     * Save block arguments in registry to use them in tags generation
     * @param \Magento\Framework\View\Layout $subject
     * @param \Closure $proceed
     * @param string $type
     * @param string $name
     * @param array $arguments
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function aroundCreateBlock(
        $subject,
        \Closure $proceed,
        $type,
        $name = '',
        array $arguments = []
    ) {
        $templates = $this->config->getHolePunchBlocks();

        if (!$this->config->isModuleEnabled() || !$templates) {
            return $proceed($type, $name, $arguments);
        }

        $result = $proceed($type, $name, $arguments);

        foreach ($templates as $template) {
            if ($result instanceof $template['block']) {
                $this->blockArgumentsRegistry->setBlockArgs($result, $arguments);

                break;
            }
        }

        return $result;
    }
}
