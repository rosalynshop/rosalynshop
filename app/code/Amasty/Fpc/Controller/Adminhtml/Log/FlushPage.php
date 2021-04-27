<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Amasty\Fpc\Model\FlushPagesManager;
use Amasty\Fpc\Model\Log;
use Amasty\Fpc\Model\ResourceModel\Log\CollectionFactory;

class FlushPage extends Action
{
    /**
     * @var CollectionFactory
     */
    private $logCollectionFactory;

    /**
     * @var FlushPagesManager
     */
    private $flushPagesManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Action\Context $context,
        CollectionFactory $logCollectionFactory,
        FlushPagesManager $flushPagesManager,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->logCollectionFactory = $logCollectionFactory;
        $this->flushPagesManager = $flushPagesManager;
        $this->logger = $logger;
    }

    /**
     * Execute action
     */
    public function execute()
    {
        try {
            $logModelId = $this->getRequest()->getParam('id');
            /** @var Log $logModel */
            $logModel = $this->logCollectionFactory->create()->getItemById($logModelId);
            $this->flushPagesManager->addPageToFlush($logModel);

            $this->messageManager->addSuccessMessage(__('Page has been flushed'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error has occurred'));
            $this->logger->critical($e);
        }

        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Fpc::log');
    }
}
