<?php

declare(strict_types=1);

namespace Manadev\Core\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Manadev\Core\Objects\LayoutEvents;

class ExecuteCallbacksBeforeGeneratingBlocks implements ObserverInterface
{
    /**
     * @var LayoutEvents
     */
    protected $layout;

    public function __construct(LayoutEvents $layout) {
        $this->layout = $layout;
    }

    public function execute(Observer $observer) {
        if (empty($this->layout->before_generating_blocks)) {
            return;

        }

        /* @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = $observer->getData('layout');

        foreach ($this->layout->before_generating_blocks as $callback) {
            $callback($layout->getUpdate());
        }
    }
}
