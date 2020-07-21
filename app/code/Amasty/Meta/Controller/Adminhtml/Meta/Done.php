<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Controller\Adminhtml\Meta;

class Done extends \Amasty\Meta\Controller\Adminhtml\Meta
{
    public function execute()
    {
        /**
         * @var $state \Magento\Framework\Indexer\StateInterface
         */
        $state = $this->_stateFactory->create();
        $state->loadByIndexer($this->getId());
        $state = $this->getState();
        $state->setStatus(\Magento\Framework\Indexer\StateInterface::STATUS_WORKING);
        $state->save();

    }
}
