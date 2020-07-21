<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Plugin\CatalogSearch\Controller\Result;

use Magento\Search\Model\QueryFactory;
use Amasty\SeoToolKit\Helper\Config;
use Magento\Search\Helper\Data as NativeData;
use Magento\Framework\App\RequestInterface;

class Index
{
    /**
     * @var Config
     */
    private $helper;

    /**
     * @var NativeData
     */
    private $searchHelper;
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        Config $helper,
        NativeData $searchHelper,
        RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->searchHelper = $searchHelper;
        $this->request = $request;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundExecute(
        $subject,
        \Closure $proceed
    ) {
        $seoKey = $this->helper->getSeoKey();
        $identifier = trim($this->request->getPathInfo(), '/');
        $identifier = explode('/', $identifier);
        $identifier = array_shift($identifier);
        if (!$this->request->isForwarded()
            && $this->helper->isSeoUrlsEnabled()
            && $seoKey
            && $seoKey != $identifier
        ) {
            // redirect to seo url
            $url = $this->searchHelper->getResultUrl($this->request->getParam(QueryFactory::QUERY_VAR_NAME));
            $subject->getResponse()->setRedirect($url);
        } else {
            return $proceed();
        }
    }
}
