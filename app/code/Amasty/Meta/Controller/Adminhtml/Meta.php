<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Controller\Adminhtml;

abstract class Meta extends \Magento\Backend\App\Action
{

    /**
     * @var \Amasty\Meta\Helper\UrlKeyHandler
     */
    protected $_helperUrl;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Indexer\Model\Indexer\StateFactory
     */
    protected $_stateFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\Meta\Helper\UrlKeyHandler $helperUrl,
        \Magento\Indexer\Model\Indexer\StateFactory $stateFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        parent::__construct($context);
        $this->_helperUrl = $helperUrl;
        $this->_stateFactory = $stateFactory;
        $this->jsonHelper = $jsonHelper;
    }

}
