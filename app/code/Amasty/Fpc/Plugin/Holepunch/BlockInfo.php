<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Plugin\Holepunch;

use Amasty\Fpc\Model\Config;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

/**
 * Plugin to save used product or category id
 */
class BlockInfo
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Config $config,
        Context $context,
        Request $request,
        Registry $registry
    ) {
        $this->config = $config;
        $this->context = $context;
        $this->request = $request;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Framework\App\PageCache\Kernel $subject
     * @param ResponseInterface $response
     */
    public function beforeProcess($subject, ResponseInterface $response)
    {
        if ($this->request->isAjax()) {
            return;
        }
        $templates = $this->config->getHolePunchBlocks();

        if ($templates && ($product = $this->registry->registry('current_product'))) {
            $response->setHeader('am_prod', $product->getId(), true);
        }
        if ($templates && ($category = $this->registry->registry('current_category'))) {
            $response->setHeader('am_cat', $category->getId(), true);
        }
    }
}
