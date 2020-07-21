<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Controller;

use Amasty\SeoToolKit\Helper\Config;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Search\Model\QueryFactory;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var Config
     */
    private $helper;

    public function __construct(
        ActionFactory $actionFactory,
        Config $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    /**
     * @param RequestInterface $request
     * @return bool|ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $seoKey = $this->helper->getSeoKey();

        if ($this->helper->isSeoUrlsEnabled() && $seoKey) {
            $identifier = trim($request->getOriginalPathInfo(), '/');
            $urlParams = explode('/', $identifier);

            if (!empty($urlParams) && count($urlParams) == 2 && $urlParams[0] == $seoKey) {
                $request->setModuleName('catalogsearch')->setControllerName('result')->setActionName('index');
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
                $params = $request->getParams();
                $params[QueryFactory::QUERY_VAR_NAME] = urldecode($urlParams[1]);
                $request->setParams($params);
                $request->setForwarded(true);

                return $this->actionFactory->create(Forward::class);
            }
        }

        return false;
    }
}
