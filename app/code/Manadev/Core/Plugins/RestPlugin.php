<?php

namespace Manadev\Core\Plugins;

use Manadev\Core\Helper;

class RestPlugin
{
    /**
     * @var Helper
     */
    protected $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function aroundDispatch($object, $proceed, ...$args) {
        return $this->helper->handleHttpAndLogAllQueries($object, $proceed, $args);
    }
}