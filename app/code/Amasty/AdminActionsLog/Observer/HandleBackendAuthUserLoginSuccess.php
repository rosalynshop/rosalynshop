<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Amasty\AdminActionsLog\Model\ActiveSessions;
use Amasty\AdminActionsLog\Model\LoginAttempts;
use Amasty\AdminActionsLog\Model\Mailsender;
use Amasty\AdminActionsLog\Model\VisitHistory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Logger;

class HandleBackendAuthUserLoginSuccess implements ObserverInterface
{
    protected $objectManager;
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $loginAttemptsModel LoginAttempts $logModel */
        $loginAttemptsModel = $this->objectManager->get(LoginAttempts::class);
        $userData = $loginAttemptsModel->prepareUserLoginData($observer, LoginAttempts::SUCCESS);
        $loginAttemptsModel->setData($userData);
        $loginAttemptsModel->save();

        /**
         * @var ActiveSessions $activeModel
         */
        $activeModel = $this->objectManager->get(ActiveSessions::class);
        $activeModel->saveActive($userData);

        /**
         * @var Mailsender $mailsendModel
         */
        $mailsendModel = $this->objectManager->get(Mailsender::class);

        $successfulMail = $this->scopeConfig->getValue('amaudit/successful_log_mailing/send_to_mail');
        if (($this->scopeConfig->getValue('amaudit/successful_log_mailing/enabled') != 0)
            && !empty($successfulMail)) {
            $mailsendModel->sendMail($userData, 'success', $successfulMail);
        }

        $suspiciousMail = $this->scopeConfig->getValue('amaudit/suspicious_log_mailing/send_to_mail');
        if ((($this->scopeConfig->getValue('amaudit/suspicious_log_mailing/enabled') != 0) &&
            !empty($suspiciousMail) && $this->scopeConfig->getValue('amaudit/geolocation/geolocation_enable'))) {
            $isSuspicious = $loginAttemptsModel->isSuspicious($userData);
            if ($isSuspicious) {
                $mailsendModel->sendMail($userData, 'suspicious', $suspiciousMail);
            }
        }

        if ($this->scopeConfig->getValue('amaudit/log/log_enable_visit_history') && !empty($userData['username'])) {
            /**
             * @var VisitHistory $visitModel
             */
            $visitModel = $this->objectManager->get(VisitHistory::class);
            $visitModel->startVisit($userData);
        }
    }
}
