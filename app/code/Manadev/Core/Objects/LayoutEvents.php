<?php

declare(strict_types=1);

namespace Manadev\Core\Objects;

use Manadev\Core\Objekt;

class LayoutEvents extends Objekt
{
    public $before_loading_xml = [];
    public $before_generating_blocks = [];

    public function beforeLoadingXml(callable $callback) {
        $this->before_loading_xml[] = $callback;
    }

    public function beforeGeneratingBlocks(callable $callback) {
        $this->before_generating_blocks[] = $callback;
    }
}
