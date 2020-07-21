<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Plugin\Framework\App\Router;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Router\NoRouteHandler as NativeNoRouteHandler;
use Magento\Search\Model\QueryFactory;

class NoRouteHandler
{
    /**
     * @var \Amasty\SeoToolKit\Helper\Config
     */
    private $config;

    /**
     * NoRouteHandler constructor.
     * @param \Amasty\SeoToolKit\Helper\Config $config
     */
    public function __construct(
        \Amasty\SeoToolKit\Helper\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param NativeNoRouteHandler $subject
     * @param $proceed
     * @param RequestInterface $request
     * @return bool
     */
    public function aroundProcess(
        NativeNoRouteHandler $subject,
        $proceed,
        RequestInterface $request
    ) {
        $pathInfo = $this->getPathInfo($request);
        if ($this->isRedirectEnabled($request) && $pathInfo) {
            $request->setParam(QueryFactory::QUERY_VAR_NAME, $pathInfo);
            $request->setModuleName('amasty_seotoolkit')->setControllerName('redirect')->setActionName('index');

            return true;
        }

        return $proceed($request);
    }

    /**
     * @param RequestInterface $request
     * @return mixed|string
     */
    private function getPathInfo(RequestInterface $request)
    {
        $pathInfo = $request->getOriginalPathInfo() ?: $request->getPathInfo();
        $pathInfo = trim($pathInfo, '/');
        $pathInfo = str_replace('/', ' ', $pathInfo);
        $pathInfo = str_replace('-', ' ', $pathInfo);
        $pathInfo = str_replace('.html', '', $pathInfo);
        $pathInfo = str_replace('.htm', '', $pathInfo);

        return $pathInfo;
    }

    /**
     * @param $request
     * @return bool
     */
    private function isRedirectEnabled($request)
    {
        $path = $request->getOriginalPathInfo();
        $exp = explode('.', $path);
        $endOfPath = end($exp);

        if ($this->config->getModuleConfig('general/four_zero_four_redirect')
            && !$request->isAjax()
            && (stristr($path, '.') === false || $endOfPath === 'html' || $endOfPath === 'htm')
        ) {
            return true;
        }

        return false;
    }
}
