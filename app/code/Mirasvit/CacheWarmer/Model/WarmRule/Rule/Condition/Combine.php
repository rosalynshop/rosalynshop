<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Model\WarmRule\Rule\Condition;

use Magento\Rule\Model\Condition\Context;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    private $pageCondition;

    private $ruleType;

    public function __construct(
        PageCondition $pageCondition,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->pageCondition     = $pageCondition;

        $this->setData('type', self::class);
    }

    public function setRuleType($type)
    {
        $this->ruleType = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $pageAttributes  = $this->pageCondition->loadAttributeOptions()->getData('attribute_option');

        $attributes = [];

        foreach ($pageAttributes as $code => $label) {
            $attributes['page'][] = [
                'value' => PageCondition::class . '|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => self::class,
                'label' => __('Conditions Combination'),
            ],
        ]);


        $conditions = array_merge_recursive($conditions, [
            [
                'label' => __('Page Attributes'),
                'value' => $attributes['page'],
            ],
        ]);

        return $conditions;
    }
}
