<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Comment;

use Magento\Framework\View\Element\Template;

class JsInit extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::comments/js.phtml';

    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('amasty_advancedreview/ajax_comment/update');
    }

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('amasty_advancedreview/ajax_comment/add');
    }
}
