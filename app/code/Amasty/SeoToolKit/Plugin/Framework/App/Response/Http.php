<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Plugin\Framework\App\Response;

use Magento\Framework\App\Response\Http as NativeHttp;

class Http
{
    /**
     * @var \Amasty\SeoToolKit\Helper\Config
     */
    private $config;

    /**
     * @var \Amasty\Base\Model\GetCustomerIp
     */
    private $customerIp;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    public function __construct(
        \Amasty\SeoToolKit\Helper\Config $config,
        \Amasty\Base\Model\GetCustomerIp $customerIp,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->config = $config;
        $this->customerIp = $customerIp;
        $this->request = $request;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @param NativeHttp $subject
     * @param $value
     *
     * @return array
     */
    public function beforeAppendBody(
        NativeHttp $subject,
        $value
    ) {
        if ($this->isEnabled() && $value) {
            $this->observeHtml($value);
        }

        return [$value];
    }

    /**
     * @return bool
     */
    private function isEnabled()
    {
        $result = (bool)$this->config->getModuleConfig('toolbar/enable');
        $result = $result && !$this->request->isAjax();
        if ($result) {
            $ip = $this->config->getModuleConfig('toolbar/ip');
            if ($ip) {
                $current = $this->customerIp->getCurrentIp();
                $ip = explode(',', $ip);
                if (!in_array($current, $ip)) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $value
     */
    private function observeHtml(&$value)
    {
        $body = '</body>';
        if (strpos($value, $body) !== false) {
            $toolbar = $this->generateHtml($value);
            $value = str_replace($body, $toolbar . $body, $value);
        }
    }

    /**
     * @param $value
     *
     * @return string
     */
    private function generateHtml($value)
    {
        $html = '';
        $block = $this->layoutFactory->create()->createBlock(
            \Amasty\SeoToolKit\Block\Toolbar::class,
            'amasty.seotoolkit.toolbar',
            [
                'data' =>
                    [
                        'html' => $value
                    ]
            ]
        );

        if ($block) {
            $html = $block->toHtml();
        }

        return $html;
    }
}
