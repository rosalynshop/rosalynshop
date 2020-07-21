<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


namespace Amasty\SeoToolKit\Plugin\Model\Menu;

use Magento\Backend\Model\Menu;

class Builder
{
    protected $seoLinks = [
        'Amasty_Meta::seotoolkit',
        'Amasty_XmlSitemap::xml_sitemap'
    ];

    /**
     * @param $subject
     * @param Menu $menu
     *
     * @return Menu
     */
    public function afterGetResult($subject, Menu $menu)
    {
        foreach ($this->seoLinks as $link) {
            $item = $menu->get($link);
            if ($item) {
                $itemsToMove = [];
                foreach ($item->getChildren() as $sort => $childItem) {
                    $itemsToMove[$childItem->getId()] = $sort;
                }

                /* fix possible error  ArrayIterator::next():
                   Array was modified outside object and internal position is no longer valid
                */
                foreach ($itemsToMove as $id => $sort) {
                    $menu->move($id, 'Amasty_SeoToolKit::seotoolkit', $sort * 100);
                }

                $menu->remove($link);
            }

        }

        return $menu;
    }
}
