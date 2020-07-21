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
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Amasty\Fpc\Model\FlushPagesManager;
use Amasty\Fpc\Model\Log;
use Amasty\Fpc\Model\ResourceModel\Log\Collection;
use Amasty\Fpc\Model\ResourceModel\Log\CollectionFactory;

class MassFlush extends Action
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $logCollectionFactory;

    /**
     * @var FlushPagesManager
     */
    private $flushPagesManager;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        CollectionFactory $logCollectionFactory,
        FlushPagesManager $flushPagesManager
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->logCollectionFactory = $logCollectionFactory;
        $this->flushPagesManager = $flushPagesManager;
    }

    /**
     * Execute mass action
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();

        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->logCollectionFactory->create());
        $flushedPages = 0;
        $failedPages = 0;

        if ($collection->count() > 0) {
            /** @var Log $log */
            foreach ($collection->getItems() as $log) {
                try {
                    $this->flushPagesManager->addPageToFlush($log);
                    $flushedPages++;
                } catch (LocalizedException $e) {
                    $failedPages++;
                } catch (\Exception $e) {
                    $this->logger->error($e);
                    $failedPages++;
                }
            }

            if ($flushedPages !== 0) {
                $this->messageManager->addSuccessMessage(
                    __('%1 page(s) has been successfully flushed', $flushedPages)
                );
            }

            if ($failedPages !== 0) {
                $this->messageManager->addErrorMessage(
                    __('%1 page(s) has been failed to flush', $failedPages)
                );
            }
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
