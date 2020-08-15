<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Controller\Track;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Service\LogServiceInterface;
use Mirasvit\CacheWarmer\Api\Service\PageServiceInterface;

/**
 * Purpose: track popularity & reports data (hit/miss, time)
 */
class Index extends Action
{
    private $pageRepository;

    private $pageService;

    private $logService;

    private $request;

    private $httpContext;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        PageServiceInterface $pageService,
        LogServiceInterface $logService,
        HttpContext $httpContext,
        Context $context
    ) {
        parent::__construct($context);

        $this->pageRepository = $pageRepository;
        $this->pageService    = $pageService;
        $this->logService     = $logService;

        $this->request     = $context->getRequest();
        $this->httpContext = $httpContext;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $uri   = $this->request->getParam('uri');
        $ttfb  = (float)$this->request->getParam('ttfb');
        $isHit = $this->request->getParam('isHit') ? true : false;

        $result = $this->increasePopularity($uri);

        $this->logService->log($uri, $ttfb, $isHit);

        /** @var \Magento\Framework\App\Response\Http\Interceptor $response */
        $response = $this->getResponse();
        $response->representJson(\Zend_Json::encode(['success' => $result]));
    }

    /**
     * @param string $uri
     * @return bool
     */
    private function increasePopularity($uri)
    {
        $varyData = $this->pageService->prepareVaryData($this->httpContext->getData());

        /** @var PageInterface $page */
        $page = $this->pageRepository->getCollection()
            ->addFieldToFilter(PageInterface::URI, $uri)
            ->addFieldToFilter(PageInterface::VARY_DATA, $varyData)
            ->getFirstItem();

        if ($page->getId()) {
            $page->setPopularity($page->getPopularity() + 1);
            $this->pageRepository->save($page);

            return true;
        }

        return false;
    }
}
