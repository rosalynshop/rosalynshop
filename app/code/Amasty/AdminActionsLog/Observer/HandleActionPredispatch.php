<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Amasty\AdminActionsLog\Model\ActiveSessions;
use Amasty\AdminActionsLog\Model\Log;
use Amasty\AdminActionsLog\Model\LoginAttempts;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class HandleActionPredispatch implements ObserverInterface
{
    protected $registryManager;
    protected $objectManager;
    protected $authSession;
    protected $appState;
    protected $scopeConfig;
    protected $helper;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Amasty\AdminActionsLog\Helper\Data $helper
    ) {
        $this->registryManager = $coreRegistry;
        $this->objectManager = $objectManager;
        $this->authSession = $authSession;
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            /**
             * @var \Magento\Framework\App\Request\Http $request
             */
            $request = $observer->getRequest();
            $action = $request->getActionName();
            if ($this->_needToRegister($action)) {
                $this->registryManager->register('amaudit_action', $action, true);
                $category = $request->getModuleName() . '/' . $request->getControllerName();
                $this->registryManager->register('amaudit_category', $category, true);
            }

            $this->_saveCache($request);
            $this->_saveExport($request);

            if ($action == 'logout') {
                /**
                 * @var LoginAttempts $loginAttemptsModel
                 */
                $loginAttemptsModel = $this->objectManager->get(LoginAttempts::class);
                $loginAttemptsModel->logout($observer);
            }

            if ($this->scopeConfig->getValue('amaudit/log/log_enable_visit_history')) {
                /**
                 * @var ActiveSessions $activeSessionsModel
                 */
                $activeSessionsModel = $this->objectManager->get(ActiveSessions::class);
                $activeSessionsModel->updateOnlineAdminActivity($this->helper->getSessionId());
            }

        }
    }

    protected function _needToRegister($action)
    {
        $needToRegister = false;

        $actionsToRegister = [
            'save',
            'edit',
            'delete',
            'massDelete',
            'deleteWebsitePost',
            'inlineEdit',
            'addComment',
            'massDisable',
            'massEnable',
            'restore',
            'cancel',
            'hold',
            'massHold',
            'unhold',
            'massUnhold',
            'post',
            'saverole',
            'massOnTheFly',
            'importPost',
        ];

        if (in_array($action, $actionsToRegister)) {
            $needToRegister = true;
        }

        return $needToRegister;
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     */
    protected function _saveExport($request)
    {
        $action = $request->getActionName();

        if (($isCsv = stripos($action, 'csv') !== false)
            || stripos($action, 'xml') !== false
            || stripos($action, 'excel') !== false
            || $action == 'exportPost'
        ) {
            $data['date_time'] = $this->objectManager->get(DateTime::class)->gmtDate();
            $data['username'] = $this->authSession->getUser()->getUserName();
            if ($isCsv) {
                $data['type'] = 'exportCsv';
            } else {
                $data['type'] = 'exportXml';
            }
            $category = $request->getModuleName() . ' ' . $request->getControllerName();
            $data['category'] = $category;
            $data['category_name'] = $category;
            $data['item'] = __('Data was exported');
            $data['store_id'] = 0;

            /** @var Log $logModel */
            $logModel = $this->objectManager->get(Log::class);
            $logModel->addData($data);
            $logModel->save();
        }
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     */
    protected function _saveCache($request)
    {
        $userId = '';
        if ($this->authSession->getUser()) {
            $userId = $this->authSession->getUser()->getId();
        }
        if ($request->getControllerName() == 'cache' && !empty($userId) && $this->helper->isUserInLog($userId)) {
            $action = $request->getActionName();

            if ($action != 'index') {
                $data['date_time'] = $this->objectManager->get(DateTime::class)->gmtDate();
                $data['username'] = $this->authSession->getUser()->getUserName();
                $data['type'] = $action;
                $data['category'] = __('Cache');
                $data['category_name'] = __('Cache');
                $data['item'] = __('Cache');
                $data['store_id'] = 0;

                /** @var Log $logModel */
                $logModel = $this->objectManager->get(Log::class);
                $logModel->addData($data);
                $logModel->save();
            }
        }
    }
}
