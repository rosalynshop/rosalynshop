<?php

declare(strict_types=1);

namespace Manadev\Core\Traits;

/**
 * @property \Magento\Framework\App\ObjectManager $singletons
 */
trait ComputedProperties
{
    protected function get_singletons() {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function __get($property) {
        return $this->$property = $this->default($property);
    }

    protected function default($property) {
        $method = "get_{$property}";
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return null;
    }
}
