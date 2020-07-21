<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block;

use \Magento\Framework\View\Element\Template;

class Tracking extends Template
{
    protected $_template = 'Amasty_Fpc::tracking.phtml';

    public function getMageInit()
    {
        return [
            'Amasty_Fpc/js/track' => [
                'url' => $this->getProcessingUrl()
            ],
        ];
    }

    private function getProcessingUrl()
    {
        return $this->_urlBuilder->getUrl('amasty_fpc/reports');
    }
}
