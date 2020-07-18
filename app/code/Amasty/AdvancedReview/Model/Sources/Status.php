<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Sources;

/**
 * Class Status
 * @package Amasty\AdvancedReview\Model\Sources
 */
class Status extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $removedStatuses = ['pending', 'fraud', 'canceled', 'holded'];
        foreach ($options as $key => $option) {
            if (isset($option['value']) && in_array($option['value'], $removedStatuses)) {
                unset($options[$key]);
            }
        }

        return $options;
    }
}
