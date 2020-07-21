<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Plugin\Framework\View\Page;

use \Magento\Framework\View\Page\Config as NativeConfig;

class Config
{
    const NO_INDEX_NO_FOLLOW = 'NOINDEX,FOLLOW';

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

    /**
     * Config constructor.
     * @param \Amasty\SeoToolKit\Helper\Config $config
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Amasty\SeoToolKit\Helper\Config $config,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * @param NativeConfig $subject
     * @param $result
     * @return string
     */
    public function afterGetDescription(
        NativeConfig $subject,
        $result
    ) {
        if ($this->config->isAddPageToMetaDescEnabled()) {
            $page = (int)$this->request->getParam($this->_pageVarName, false);
            if ($page) {
                $result .= __(' | Page %1', $page);
            }
        }

        return $result;
    }

    /**
     * @param NativeConfig $subject
     * @param $result
     * @return string
     */
    public function afterGetRobots(
        NativeConfig $subject,
        $result
    ) {
        if ($this->config->isNoIndexNoFollowEnabled()
            && $this->request->getModuleName() === 'catalogsearch'
        ) {
            $result = self::NO_INDEX_NO_FOLLOW;
        }

        return $result;
    }
}
