<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml;

use Magento\Backend\Block\Template;

class Notice extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Fpc::notice.phtml';

    public function getText()
    {
        return __(
            'The following CLI commands are available: <br/>bin/magento '
            . 'fpc:warmer:generate <br/>bin/magento fpc:warmer:process'
        );
    }
}
