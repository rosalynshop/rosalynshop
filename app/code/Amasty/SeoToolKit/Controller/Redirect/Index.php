<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Controller\Redirect;

use Magento\Framework\App\Action\Context;
use Magento\Search\Model\QueryFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    private $searchHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Search\Helper\Data $searchHelper
     */
    public function __construct(
        Context $context,
        \Magento\Search\Helper\Data $searchHelper
    ) {
        parent::__construct($context);
        $this->searchHelper = $searchHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * |\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->searchHelper->getResultUrl($this->_request->getParam(QueryFactory::QUERY_VAR_NAME));
        $resultRedirect->setUrl($url);

        return $resultRedirect;
    }
}
