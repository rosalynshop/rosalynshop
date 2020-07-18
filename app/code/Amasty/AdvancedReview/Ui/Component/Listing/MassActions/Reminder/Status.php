<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Ui\Component\Listing\MassActions\Reminder;

/**
 * Class Status
 * @package Amasty\AdvancedReview\Ui\Component\Listing\MassActions\Reminder
 */
class Status extends \Amasty\AdvancedReview\Ui\Component\Listing\MassActions\MassAction
{
    /**
     * {@inheritdoc}
     */
    public function getUrlParams($optionValue)
    {
        return ['status' => $optionValue];
    }
}
