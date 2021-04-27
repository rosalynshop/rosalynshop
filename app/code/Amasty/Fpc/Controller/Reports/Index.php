<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Controller\Reports;

use Amasty\Fpc\Model\ReportsFactory;
use Amasty\Fpc\Model\ResourceModel\Reports;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManager;

class Index extends Action
{
    const HIT_STATUS = 'hit';
    const MISS_STATUS = 'miss';

    /**
     * @var ReportsFactory
     */
    private $reportsFactory;

    /**
     * @var Reports
     */
    private $reportsResource;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    public function __construct(
        Context $context,
        ReportsFactory $reportsFactory,
        Reports $reports,
        SessionManager $sessionManager
    ) {
        parent::__construct($context);
        $this->reportsFactory = $reportsFactory;
        $this->reportsResource = $reports;
        $this->request = $context->getRequest();
        $this->sessionManager = $sessionManager;
    }

    public function execute()
    {
        $status = $this->sessionManager->getPageStatus();

        if ($status !== self::HIT_STATUS) {
            $status = self::MISS_STATUS;
        }

        $resposneTime = (float)$this->request->getParam('ttfb');

        $report = $this->reportsFactory->create();
        $report->setData(['status' => $status, 'response' => $resposneTime]);
        $this->reportsResource->save($report);
    }
}
