<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Plugin;

use Magento\Framework\UrlInterface;
use Amasty\Fpc\Model\FlushPagesManager;

class OnePageCacheFlush
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var FlushPagesManager
     */
    private $flushPagesManager;

    public function __construct(
        UrlInterface $url,
        FlushPagesManager $flushPagesManager
    ) {
        $this->url = $url;
        $this->flushPagesManager = $flushPagesManager;
    }

    /**
     * Plagin to disable cache load if page need to be flushed
     *
     * @param Magento\Framework\App\PageCache\Kernel $subject
     * @param \Closure $proceed
     *
     * @return bool|mixed
     */
    public function aroundLoad($subject, \Closure $proceed)
    {
        $currentUrl = $this->url->getCurrentUrl();
        if ($page = $this->flushPagesManager->findPageToFlush($currentUrl)) {
            $this->flushPagesManager->deletePageToFlush($page);

            return false;
        }
        
        return $proceed();
    }
}
