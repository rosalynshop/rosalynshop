<?php

namespace Manadev\Core;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

abstract class AttributeSource extends AbstractSource
{
    abstract public function getOptions();

    /**
     * @inheritDoc
     */
    public function getAllOptions() {
        $result = [];
        foreach($this->getOptions() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }
}