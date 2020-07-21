<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Plugin\Catalog\Controller\Product;

use Amasty\SeoSingleUrl\Helper\Data;
use Amasty\SeoSingleUrl\Model\Source\Type;
use Magento\Catalog\Controller\Product\View as MagentoView;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class View
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Session
     */
    private $catalogSession;

    public function __construct(
        Data $helper,
        StoreManagerInterface $storeManager,
        Session $catalogSession
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->catalogSession = $catalogSession;
    }

    /**
     * @param MagentoView $subject
     * @param \Closure $proceed
     *
     * @return ResponseInterface|mixed|null
     * @throws NoSuchEntityException
     */
    public function aroundExecute(
        MagentoView $subject,
        \Closure $proceed
    ) {
        $result = null;
        $request = $subject->getRequest();
        $redirect = $this->helper->getModuleConfig('general/force_redirect');
        $type = $this->helper->getModuleConfig('general/product_url_type');

        if ($redirect
            && $type !== Type::DEFAULT_RULES
            && !(int)$request->getParam('amasty_quickview', 0)
            && !$request->getParam('amoptimizer_bundle_check')
        ) {
            $productId = (int)$request->getParam('id');

            if ($productId) {
                $canonicalUrl = $this->helper->generateSeoUrl($productId, $this->storeManager->getStore()->getId());

                if ($canonicalUrl) {
                    $originalPath = ltrim($request->getOriginalPathInfo(), '/');

                    if ($originalPath && $canonicalUrl !== $originalPath) {
                        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
                        $result = $subject->getResponse();
                        $result->setRedirect($baseUrl . $canonicalUrl, 301)->sendResponse();
                    }
                }
            }
        }

        if (!$result) {
            /* remove wrong category id from request (it is wrong, because we changed category params in url)*/
            $category = $this->catalogSession->getLastVisitedCategoryId();
            $type = $this->helper->getModuleConfig('general/product_url_type');

            if ($type !== Type::DEFAULT_RULES && $category) {
                $request->setParam('category', 0);
            }

            $result = $proceed();
        }

        return $result;
    }
}
