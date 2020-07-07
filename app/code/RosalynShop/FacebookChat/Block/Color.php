<?php
/**
 * @author   Rosalynshop <info@rosalynshop.com>
 * @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\FacebookChat\Block;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Color extends Field
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Color constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $output=parent::_getElementHtml($element);
        $output .= "
		<script type='text/javascript'>
			require([
				'jquery'
			], function(jQuery){
				(function($) {
					$('#".$element->getHtmlId()."').attr('data-hex', true).mColorPicker();
				})(jQuery);
			});
		</script>";
        return $output;
    }
}
