<?php
/**
 *  @author   Rosalynshop <info@rosalynshop.com>
 *  @copyright Copyright (c) 2019 Rosalynshop <https://rosalynshop.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 3.0
 */

namespace RosalynShop\RegionManager\Block\Adminhtml\System\Config\Buttons;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ImportCitiesListButton
 * @package RosalynShop\RegionManager\Block\Adminhtml\System\Config\Buttons
 */
class ImportCitiesListButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Import'),
            'class' => 'import primary',
            'on_click' => sprintf("location.href = '%s';", $this->path()),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'import']],
                'form-role' => 'import',
            ],
            'sort_order' => 90,
        ];
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this->getUrl('regionmanager/importCitiesList/import');
    }

    /**
     * @param AbstractElement $element
     * @return mixed
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'id' => 'import_cities_list',
                'label' => __('Import')
            ]);
        return $button->toHtml();
    }
}