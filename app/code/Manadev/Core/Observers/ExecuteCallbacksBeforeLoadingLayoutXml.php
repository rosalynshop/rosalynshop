<?php

declare(strict_types=1);

namespace Manadev\Core\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Manadev\Core\Objects\LayoutEvents;

class ExecuteCallbacksBeforeLoadingLayoutXml implements ObserverInterface
{
    /**
     * @var LayoutEvents
     */
    protected $layout;

    public function __construct(LayoutEvents $layout) {
        $this->layout = $layout;
    }

    public function execute(Observer $observer) {
        if (empty($this->layout->before_loading_xml)) {
            return;

        }

        /* @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = $observer->getData('layout');

        foreach ($this->layout->before_loading_xml as $callback) {
            $callback($layout->getUpdate());
        }
    }
}
