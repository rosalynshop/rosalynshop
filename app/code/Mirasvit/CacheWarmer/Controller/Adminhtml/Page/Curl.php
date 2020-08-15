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



namespace Mirasvit\CacheWarmer\Controller\Adminhtml\Page;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Service\WarmerServiceInterface;
use Mirasvit\CacheWarmer\Controller\Adminhtml\AbstractPage;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Service\CurlService;

class Curl extends AbstractPage
{
    private $curlService;

    public function __construct(
        CurlService $curlService,
        PageRepositoryInterface $pageRepository,
        WarmerServiceInterface $warmerService,
        Config $config,
        Filter $filter,
        Context $context
    ) {
        $this->curlService = $curlService;

        parent::__construct($pageRepository, $warmerService, $config, $filter, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $page = $this->pageRepository->get($this->getRequest()->getParam(PageInterface::ID));

        $channel = $this->curlService->initChannel();

        $userAgent = $this->warmerService->getUserAgent(
            $page->getVaryData(),
            $page->getProductId(),
            $page->getCategoryId()
        );

        $channel->setUrl($page->getUri());
        $channel->setUserAgent($userAgent);

        //we add this cookie to have correct cache hit stats
        $channel->addCookie('mst-cache-warmer-track', 1);
        
        if ($page->getVaryString()) {
            $channel->addCookie('X-Magento-Vary', $page->getVaryString());
        }

        $this->messageManager->addNoticeMessage(
            $channel->getCUrl()
        );

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
