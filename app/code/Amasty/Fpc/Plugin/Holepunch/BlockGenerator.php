<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Plugin\Holepunch;

use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\HolePunch\HolePunchProcessor;
use Amasty\Fpc\Model\HolePunch\BlockTagsMarker;

/**
 * Replacing holepunch tags with new blocks
 */
class BlockGenerator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var HolePunchProcessor
     */
    private $holePunchProcessor;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config,
        HolePunchProcessor $holePunchProcessor
    ) {
        $this->config = $config;
        $this->holePunchProcessor = $holePunchProcessor;
    }

    /**
     * @param \Magento\PageCache\Model\App\FrontController\BuiltinPlugin $subject
     * @param \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\Response\Http $result
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\Response\Http
     */
    public function afterAroundDispatch($subject, $result)
    {
        if (!($result instanceof \Magento\Framework\App\Response\Http)
            || !$this->config->isModuleEnabled()
        ) {
            return $result;
        }
        $content = $result->getContent();

        if ($content === "") {
            return $result;
        }
        preg_match_all(
            BlockTagsMarker::SEARCH_PATTERN,
            $content, $containers, PREG_PATTERN_ORDER
        );
        $containers = array_unique($containers[1]);

        if ($containers) {
            $result = $this->holePunchProcessor->processPageCache($containers, $result);
        }

        return $result;
    }
}
