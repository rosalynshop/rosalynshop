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



namespace Mirasvit\CacheWarmer\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\PageCache\Identifier as CacheIdentifier;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Element\Template;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;

class ToolbarJs extends Template
{
    const COOKIE = 'mst-cache-warmer-toolbar';

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;


    /**
     * @var CacheIdentifier
     */
    private $cacheIdentifier;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;


    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        CookieManagerInterface $cookieManager,
        CacheIdentifier $cacheIdentifier,
        PageRepositoryInterface $pageRepository,
        Context $context
    ) {
        $this->cookieManager   = $cookieManager;
        $this->cacheIdentifier = $cacheIdentifier;
        $this->pageRepository  = $pageRepository;

        $this->urlBuilder = $context->getUrlBuilder();

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getMageInit()
    {
        return [
            'Mirasvit_CacheWarmer/js/toolbar' => [
                'cookieName'  => self::COOKIE,
                'cookieValue' => $this->cookieManager->getCookie(self::COOKIE),
                'pageId'      => $this->getPageId(),
                'toolbarUrl'  => $this->urlBuilder->getUrl('cache_warmer/toolbar'),
            ],
        ];
    }

    /**
     * @return string|false
     */
    public function getPageId()
    {
        $cacheId = $this->cacheIdentifier->getValue();

        $page = $this->pageRepository->getByCacheId($cacheId);

        return $page ? $page->getId() : $cacheId;
    }
}
