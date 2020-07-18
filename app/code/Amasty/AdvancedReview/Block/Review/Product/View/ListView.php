<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Review\Product\View;

class ListView extends \Magento\Review\Block\Product\View\ListView
{
    /**
     * Prepare product review list toolbar
     *
     * @return \Amasty\AdvancedReview\Block\Review\Product\View\ListView
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->getBlock('product_review_list.toolbar');
        if ($toolbar) {
            $toolbar->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }
}
