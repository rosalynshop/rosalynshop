<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml\Meta;

class Process extends \Amasty\Meta\Controller\Adminhtml\Meta
{
    public function execute()
    {
        $request = $this->getRequest();
        $template = trim($request->getParam('template'));

        if (!empty($template)) {
            $key       = $request->getParam('store_key');
            $storeKeys = [($key ? $key : 'admin')];
            $page      = $request->getParam('page');

            $this->_helperUrl->process($template, $storeKeys, $page);
        }

    }
}
