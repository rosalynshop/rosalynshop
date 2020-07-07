<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\SocialLogin\Controller\Social;

/**
 * Class Callback
 *
 * @package MageBig\SocialLogin\Controller\Social
 */
class Callback extends AbstractSocial
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($this->checkRequest('hauth_start', false) && (
                $this->checkRequest('error_reason', 'user_denied')
                && $this->checkRequest('error', 'access_denied')
                && $this->checkRequest('error_code', '200')
                && $this->checkRequest('hauth_done', 'Facebook')
                || ($this->checkRequest('hauth_done', 'Twitter') && $this->checkRequest('denied'))
            )) {
            return $this->_appendJs(sprintf("<script>window.close();</script>"));
        }

        \Hybrid_Endpoint::process();
    }

    /**
     * @param $key
     * @param null $value
     * @return bool|mixed
     */
    public function checkRequest($key, $value = null)
    {
        $param = $this->getRequest()->getParam($key, false);

        if ($value) {
            return $param == $value;
        }

        return $param;
    }
}