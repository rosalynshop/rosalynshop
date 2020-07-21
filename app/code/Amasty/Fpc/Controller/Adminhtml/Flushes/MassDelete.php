<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Controller\Adminhtml\Flushes;

use Amasty\Fpc\Model\FlushesLog;
use Amasty\Fpc\Model\Repository\FlushesLogRepository;
use Amasty\Fpc\Model\ResourceModel\FlushesLog\Collection;
use Amasty\Fpc\Model\ResourceModel\FlushesLog\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends Action
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
    private $flushesLogCollectionFactory;

    /**
     * @var FlushesLogRepository
     */
    private $flushesLogRepository;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        FlushesLogRepository $flushesLogRepository,
        CollectionFactory $flushesLogCollectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->flushesLogCollectionFactory = $flushesLogCollectionFactory;
        $this->flushesLogRepository = $flushesLogRepository;
    }

    /**
     * Execute mass action
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();

        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->flushesLogCollectionFactory->create());

        if ($collection->count() > 0) {
            /** @var FlushesLog $flushesLog */
            foreach ($collection->getItems() as $flushesLog) {
                try {
                    $this->flushesLogRepository->delete($flushesLog);
                } catch (\Exception $e) {
                    $this->logger->error($e);
                }
            }

            $this->messageManager->addSuccessMessage(__('Logs was successfully removed.'));
        }

        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Fpc::clear_flushes_log');
    }
}
