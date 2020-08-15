<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Number
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Number\Plugin;

class SequencePlugin
{
    /**
     * @var \Amasty\Number\Helper\Data
     */
    protected $helper;

    /**
     * @var \Amasty\Number\Model\SequenceConfiguration
     */
    private $sequenceConfiguration;

    /**
     * @var string
     */
    protected $type = 'order';

    /**
     * SequencePlugin constructor.
     *
     * @param \Amasty\Number\Helper\Data                 $helper
     * @param \Amasty\Number\Model\SequenceConfiguration $sequenceConfiguration
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        \Amasty\Number\Helper\Data $helper,
        \Amasty\Number\Model\SequenceConfiguration $sequenceConfiguration
    ) {
        $this->helper = $helper;
        $this->sequenceConfiguration = $sequenceConfiguration;
    }

    /**
     * Retrieve new incrementId
     *
     * @param \Magento\Framework\DB\Sequence\SequenceInterface $subject
     * @param string                                           $incrementId
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetNextValue(
        \Magento\Framework\DB\Sequence\SequenceInterface $subject,
        $incrementId
    ) {
        $storeId = $this->sequenceConfiguration->getStoreId();
        if (!isset($storeId)
            || $this->sequenceConfiguration->getEntityType() != $this->type
            || !$this->helper->getConfigValueByPath('amnumber/general/enabled', $storeId)
            || $this->helper->getConfigValueByPath('amnumber/' . $this->type . '/same', $storeId)
        ) {
            return $incrementId;
        }

        $newIncrementId = $this->helper->getFormatIncrementId($this->type, $storeId, $incrementId);
        $this->helper->flushConfigCache();

        return $newIncrementId ? : $incrementId;
    }
}
