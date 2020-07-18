<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\Review\Block\Product;

/**
 * Class ReviewPlugin
 * With page builder Full Layout magento shows content without tabs - review is not showed.
 * Js script required to have tab-label-reviews element
 */
class ReviewPlugin
{
    /**
     * @param \Magento\Review\Block\Product\Review $subject
     * @param $html
     *
     * @return string
     */
    public function afterToHtml(\Magento\Review\Block\Product\Review $subject, $html)
    {
        $parent = $subject->getParentBlock();

        if ($parent && $subject->getNameInLayout() == 'reviews.tab'
            && $parent->getNameInLayout() == 'product.reviews.wrapper'
        ) {
            $html .= '<div id="tab-label-reviews" role="tab" class="active"></div>';
        }

        return $html;
    }
}
