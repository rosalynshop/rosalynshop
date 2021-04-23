<?php

namespace Manadev\Core;

use Manadev\Core\Traits\ComputedProperties;

/**
 * Generic base class
 */
class Objekt
{
    use ComputedProperties;

    public function __construct($data = []) {
        foreach ($data as $property => $value) {
            $this->$property = $value;
        }
    }
}
