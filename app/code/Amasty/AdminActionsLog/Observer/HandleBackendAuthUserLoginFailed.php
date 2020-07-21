<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Amasty\AdminActionsLog\Model\LoginAttempts;
use Amasty\AdminActionsLog\Model\Mailsender;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Logger;

class HandleBackendAuthUserLoginFailed implements ObserverInterface
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
        $loginAttemptsModel = $this->objectManager->create(LoginAttempts::class);
        $userData = $loginAttemptsModel->prepareUserLoginData($observer, LoginAttempts::UNSUCCESS);
        $loginAttemptsModel->setData($userData);
        $loginAttemptsModel->save();

        $receiveUnsuccessfulEmail = $this->scopeConfig->getValue('amaudit/unsuccessful_log_mailing/send_to_mail');
        if (($this->scopeConfig->getValue('amaudit/unsuccessful_log_mailing/enabled') != 0)
            && !empty($receiveUnsuccessfulEmail)) {
            $unsuccessfulCount = $loginAttemptsModel->getUnsuccessfulCount();
            if ($unsuccessfulCount >= $loginAttemptsModel::MIN_UNSUCCESSFUL_COUNT) {
                $userData['unsuccessful_login_count'] = $unsuccessfulCount;
                /**
                 * @var Mailsender $mailsendModel
                 */
                $mailsendModel = $this->objectManager->get(Mailsender::class);
                $userData['unsuccessful_login_count'] = $unsuccessfulCount;
                $mailsendModel->sendMail($userData, 'unsuccessful', $receiveUnsuccessfulEmail);
            }
        }
    }
}
