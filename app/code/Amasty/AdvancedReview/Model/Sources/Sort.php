<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Data\OptionSourceInterface;

class Sort implements OptionSourceInterface
{
    const HELPFUL = 'helpful';

    const HELPFUL_ALIAS = 'helpful';

    const TOP_RATED = 'top_rated';

    const TOP_RATED_ALIAS = 'rating_summary';

    const NEWEST = 'newest';

    const NEWEST_ALIAS = 'main_table.created_at';

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $config;

    public function __construct(\Amasty\AdvancedReview\Helper\Config $config)
    {
        $this->config = $config;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $data = [
            [
                'value' => self::NEWEST,
                'label' => __('Date')
            ],
            [
                'value' => self::TOP_RATED,
                'label' => __('Rating')
            ]
        ];

        if ($this->config->isAllowHelpful()) {
            $data[] =  [
               'value' => self::HELPFUL,
               'label' => __('Helpfulness')
            ];
        }

        return $data;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        $optionArray = $this->toOptionArray();
        $labels =  array_column($optionArray, 'label');
        $values =  array_column($optionArray, 'value');
        return array_combine($values, $labels);
    }
}
