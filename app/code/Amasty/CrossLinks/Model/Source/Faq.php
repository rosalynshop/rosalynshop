<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model\Source;

/**
 * Class TargetType
 * @package Amasty\CrossLinks\Model\Source
 */
class Faq implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->moduleManager->isEnabled('Amasty_Faq')) {
            $result = [0 => __('No'), 1 => __('Yes')];
        } else {
            $result = [0 => __('No (Module was not installed)')];
        }

        return $result;
    }
}
