<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Plugin\Framework\View\Page;

use \Magento\Framework\View\Page\Title as NativeTitle;

class Title
{
    /**
     * @var string
     */
    protected $_pageVarName = 'p';

    /**
     * @var \Amasty\SeoToolKit\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Amasty\SeoToolKit\Helper\Config $config,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * @param NativeTitle $subject
     * @param $result
     * @return string
     */
    public function afterGet(
        NativeTitle $subject,
        $result
    ) {
        if ($this->config->isAddPageToMetaTitleEnabled()) {
            $page = (int)$this->request->getParam($this->_pageVarName, false);
            if ($page) {
                $result .= __(' | Page %1', $page);
            }
        }

        return $result;
    }
}
