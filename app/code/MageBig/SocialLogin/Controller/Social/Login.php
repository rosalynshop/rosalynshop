<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\SocialLogin\Controller\Social;

/**
 * Class Login
 * @package MageBig\SocialLogin\Controller\Social
 */
class Login extends AbstractSocial
{
    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute()
    {
        if($this->session->isLoggedIn()){
            $this->_redirect('customer/account');
            return;
        }

        $type = $this->apiHelper->setType($this->getRequest()->getParam('type', null));
        if (!$type) {
            $this->_forward('noroute');

            return;
        }

        try {
            $userProfile = $this->apiObject->getUserProfile($type);
            if (!$userProfile->identifier) {
                return $this->emailRedirect($type);
            }
        } catch (\Exception $e) {
            $this->setBodyResponse($e->getMessage());

            return;
        }

        $customer = $this->apiObject->getCustomerBySocial($userProfile->identifier, $type);
        if (!$customer->getId()) {
            if (!$userProfile->email && $this->apiHelper->requireRealEmail()) {
                $this->session->setUserProfile($userProfile);

                return $this->_appendJs(sprintf("<script>window.close();window.opener.fakeEmailCallback('%s');</script>", $type));
            }
            $customer = $this->createCustomerProcess($userProfile, $type);
        }
        $this->refresh($customer);

        return $this->_appendJs();
    }

    /**
     * @param $message
     */
    protected function setBodyResponse($message)
    {
        $content = '<html><head></head><body>';
        $content .= '<div class="message message-error">' . __("Ooophs, we got an error: %1", $message) . '</div>';
        $content .= <<<Style
<style type="text/css">
    .message{
        background: #fffbbb;
        border: none;
        border-radius: 0;
        color: #333333;
        font-size: 1.4rem;
        margin: 0 0 10px;
        padding: 1.8rem 4rem 1.8rem 1.8rem;
        position: relative;
        text-shadow: none;
    }
    .message-error{
        background:#ffcccc;
    }
</style>
Style;
        $content .= '</body></html>';

        $this->getResponse()->setBody($content);
    }
}