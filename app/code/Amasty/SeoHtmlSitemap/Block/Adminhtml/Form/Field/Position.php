<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_SeoHtmlSitemap
 */


namespace Amasty\SeoHtmlSitemap\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Amasty\SeoHtmlSitemap\Helper\Data as SitemapHelper;
use Magento\Framework\Module\Manager;

class Position extends Field
{
    /**
     * @var SitemapHelper
     */
    private $helper;

    /**
     * @var Manager
     */
    private $manager;

    public function __construct(
        SitemapHelper $helper,
        Context $context,
        Manager $manager
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->manager = $manager;
    }

    protected function _construct()
    {
        $this->setTemplate('Amasty_SeoHtmlSitemap::form/field/position.phtml');
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_toHtml();
    }

    /**
     * @return array|null
     */
    public function getPositions()
    {
        $positions =  (array) $this->helper->getSortOrder();
        if ($this->manager->isEnabled('Amasty_Xlanding')) {
            $positions['landing_pages'] = 'Landing pages';
        } else {
            unset($positions['landing_pages']);
        }

        return $positions;
    }

    /**
     * @param $index
     * @return string
     */
    public function getNamePrefix($index)
    {
        return $this->getElement()->getName() . '[' . $index . ']';
    }
}
