<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml\Meta;

class Init extends \Amasty\Meta\Controller\Adminhtml\Meta
{
    public function execute()
    {
        $request  = $this->getRequest();
        $key = $request->getParam('store_key');
        $storeKeys = [($key ? $key : 'admin')];
        $total = $this->_helperUrl->estimate($storeKeys);

        $this->getResponse()->setBody(
            $this->jsonHelper->jsonEncode([
                'total' => $total,
                'page_size' => $this->_helperUrl->getPageSize()
            ])
        );
    }
}
