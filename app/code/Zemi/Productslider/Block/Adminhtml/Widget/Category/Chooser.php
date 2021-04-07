<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\Productslider\Block\Adminhtml\Widget\Category;

class Chooser extends \Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser
{
    /**
     * Block construction
     * Defines tree template and init tree params
     *
     * @var string
     */
    protected $_template = 'Zemi_Productslider::widget/catalog/category/widget/tree.phtml';

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param \Magento\Framework\Data\Tree\Node|array $node
     * @param int $level
     *
     * @return array
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = parent::_getNodeJson($node, $level);
        if (in_array($node->getId(), $this->getSelectedCategories())) {
            $item['checked'] = true;
        }
        $item['is_anchor'] = (int)$node->getIsAnchor();
        $item['level'] = (int)$level;
        $item['url_key'] = $node->getData('url_key');

        return $item;
    }
}
