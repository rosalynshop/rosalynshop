<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoHtmlSitemap
 */


namespace Amasty\SeoHtmlSitemap\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class LandingPage extends AbstractHelper
{

    protected $_landingPageFactory;

    protected $_storeManager;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Amasty\SeoHtmlSitemap\Model\Page\Xlanding\PageFactory $landingPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_landingPageFactory = $landingPageFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getPageUrl($pageId = null)
    {
        $landingPage = $this->_landingPageFactory->create();
        if ($pageId !== null && $pageId !== $landingPage->getId()) {
            $landingPage->setStoreId($this->_storeManager->getStore()->getId());
            if (!$landingPage->load($pageId)) {
                return null;
            }
        }

        if (!$landingPage->getId()) {
            return null;
        }

        return $this->_urlBuilder->getUrl(null, ['_direct' => $landingPage->getIdentifier()]);
    }
}